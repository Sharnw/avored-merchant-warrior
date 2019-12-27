<?php
namespace Sharnw\AvoRedMerchantWarrior;

use Sharnw\AvoRedMerchantWarrior\API\Request;
use AvoRed\Framework\Database\Contracts\ConfigurationModelInterface;
use AvoRed\Framework\Support\Contracts\PaymentOptionInterface;
use Sharnw\AvoRedMerchantWarrior\Models\MerchantWarriorTransaction as Transaction;
use AvoRed\Framework\Database\Models\Address;
use AvoRed\Framework\Database\Models\Country;
use AvoRed\Framework\Database\Models\Order;
use AvoRed\Framework\Support\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use App\User;
use App;

class CardPayment implements PaymentOptionInterface
{
    
    const CONFIG_KEY = 'payment_merchant_warrior_card_enabled';
    
    /**
     * Identifier for this Payment Option.
     *
     * @var string
     */
    protected $identifier = 'mw-card';
    
    /**
     * Title for this Payment Option.
     *
     * @var string
     */
    protected $name = 'Card Payment (MW)';
    
    /**
     * Payment options View Path.
     *
     * @var string
     */
    protected $view = 'mw-card::index';

    /**
     * Attached address model for use in payment processing.
     *
     * @var \AvoRed\Framework\Database\Models\Address
     */
    protected $address;

    /**
     * Last transaction processed by this payment option.
     *
     * @var Transaction
     */
    public $lastTransaction;
    
    /**
     * Get Identifier for this Payment Option.
     *
     * return string
     */
    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * Check whether this payment option requires attachment of address data.
     *
     * @var boolean
     */
    public function requiresAddress() {
        return true;
    }

    /**
     * Set attached address data.
     *
     * @param \AvoRed\Framework\Database\Models\Address
     */
    public function setAddress(Address $address) {
        $this->address = $address;
    }

    /**
     * Get attached address data.
     *
     * @return \AvoRed\Framework\Database\Models\Address | null
     */
    public function address() {
        return $this->address;
    }

    /**
     * Attempt to process a card payment.
     *
     * return Sharnw\AvoRedMerchantWarrior\Models\MerchantWarriorTransaction|null
     */
    public function process()
    {
        // total amount from the shopping cart
        $totalAmount = Cart::total();

        // card details
        $card = request()->get('card');

        // user object
        $user = Auth::user();
        // default currency code
        $currencyCode = strtoupper(session()->get('default_currency')->code);

        // merchant warrior API wrapper
        $api = App::make('mwarr');

        // customer information fields
        $customer = [
            'customerName' => $user->name,
            'customerEmail' => $user->email,
            'customerIP' => request()->ip()
        ];
        // grab attached address data
        $address = $this->address();

        if ($address) {
            $customer = array_merge($customer, [
                'customerCountry' => strtoupper($address->country->code),
                'customerState' => strtoupper($address->state),
                'customerCity' => $address->city,
                'customerAddress' => $address->address1.($address->address2 != '' ? ' '.$address->address2 : ''),
                'customerPostCode' => $address->postcode,
                'customerPhone' => $address->phone,
            ]);
        } else {
            Log::error('No billing address provided with payment method.');
            throw new \Exception('No billing address provided with payment method.');
        }

        // card detail fields
        $card = [
          'paymentCardName' => $user->name,
          'paymentCardNumber' => $card['number'],
          'paymentCardExpiry' => $card['exp_month'].$card['exp_year'],
          'paymentCardCSC' => $card['cvc']
        ];

        // attempt processCard request
        $result = $api->processCard($totalAmount, $currencyCode, $customer, $card);

        if ($result->getSuccess()) {
            $this->lastTransaction = Transaction::create([
                'transaction_id' => $result->getTransactionID(),
                'transaction_ref' => $result->getTransactionRef(),
            ]);
        }
    }

    /**
     * Attempts to associate an order with the last transaction.
     *
     * @param \AvoRed\Framework\Database\Models\Order $order
     */
    public function syncOrder(Order $order) {
        $this->lastTransaction->order()->associate($order);
    }

    /**
     * Retrieve transaction for a given orderId.
     *
     * return Sharnw\AvoRedMerchantWarrior\Models\MerchantWarriorTransaction|null
     */
    public function getTransactionByOrderId($orderId = null)
    {
        return Transaction::where('order_id', $orderId)->firstOrFail();
    }
    
    /**
     * Get Title for this Payment Option.
     *
     * return boolean
     */
    public function name()
    {
        return $this->name;
    }
    
    /**
     * Payment Option View Path.
     * return String
     */
    public function view()
    {
        return $this->view;
    }

     /**
     * Render Payment Option
     * return String
     */
    public function render()
    {
        return view($this->view())->with($this->with());
    }
    
    
    /**
     * Payment Option View Data.
     *
     * return Array
     */
    public function with()
    {
        return ['payment' => $this];
    }
}

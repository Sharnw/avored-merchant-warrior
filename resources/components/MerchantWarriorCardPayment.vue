<template>
    <div>
        <payment-option-toggle identifier="mw-card" label="Card Payment"></payment-option-toggle>
        <div v-if="cardPaymentToggled">
            <a-divider>
                <h4 class="mt-1">Card Payment Details</h4>
            </a-divider>
            <merchant-warrior-card-form></merchant-warrior-card-form>
        </div>
    </div>    
</template>

<script>

export default {
    name: 'merchant-warrior-card-payment',
    props: [],
    data () {
        return {
            cardPaymentToggled: false,
            cardValid: false
        }
    },
    mounted() {        
        var cardPayment = this;
        // attempt a payment
        EventBus.$on('placeOrderBefore', function() {
            // check if the event was for this payment option
            if (cardPayment.cardPaymentToggled) {
                // make sure card has been validated
                if (!cardPayment.cardValid) {
                    // if not let's fire the validation method
                    // at the very least this will show the error messages
                    EventBus.$emit('requestCardValidation');
                    return;
                }

                // if successful we submit order
                EventBus.$emit('placeOrderAfter');
            }
        });
        // show card details form if this payment option has been toggled
        EventBus.$on('selectedPaymentIdentifier', function(identifier) {
            cardPayment.cardPaymentToggled = (identifier == 'mw-card');
        });
        // allow order attempt to be submitted once card is validated
        EventBus.$on('cardIsValid', function() {
            cardPayment.cardValid = true;
        });
    }
}
</script>

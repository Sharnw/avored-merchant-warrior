<template>
    <div>
        <a-row :gutter="10">
            <a-col :span="18">
                <a-form-item
                        :validate-status="cardNumberError == '' ? '' : 'error'"
                        :help="cardNumberError"
                        label="Card Number">
                    <a-input
                        name="card[number]"
                        maxLength="16"
                        v-model="card.number"
                    />
                </a-form-item>
            </a-col>
            <a-col :span="6">
                <a-form-item
                        :validate-status="cvcError == '' ? '' : 'error'"
                        :help="cvcError"
                        label="CVC">
                    <a-input 
                        name="card[cvc]"
                        maxLength="4"
                        v-model="card.cvc"
                    />
                </a-form-item>
            </a-col>
        </a-row>
        <a-row :gutter="10">
            <a-col :span="12">
                <a-form-item
                        :validate-status="expiryMonthError == '' ? '' : 'error'"
                        :help="expiryMonthError"
                        label="Expiry Month">
                    <a-input 
                        name="card[exp_month]"
                        :value="card.exp_month"
                        maxLength="2"
                        v-model="card.exp_month"
                    />
                </a-form-item>
            </a-col>
            <a-col :span="12">
                <a-form-item
                        :validate-status="expiryYearError == '' ? '' : 'error'"
                        :help="expiryYearError"
                        label="Expiry Year">
                    <a-input 
                        name="card[exp_year]"
                        maxLength="2"
                        v-model="card.exp_year"
                    />
                </a-form-item>
            </a-col>
        </a-row>
        <a-button
            type="primary"
            v-on:click.prevent="validateCard()"
        >
            Validate Card
        </a-button>
        <p v-if="valid">Card details are valid.</p>
    </div>
</template>

<script>
import cardValidator from 'card-validator';

export default {
    data() {
        return {
            card: {
                number: null,
                cvc: null,
                exp_month: null,
                exp_year: null
            },
            cardNumberError: '',
            expiryMonthError: '',
            expiryYearError: '',
            cvcError: '',
            valid: false
        }
    },
    methods: {
        validateCard() {
            console.log('-- VALIDATING CARD --');
            // run basic client-side card validation to check for obvious errors
            this.valid = true;
            // validate card number
            var numberValidation = cardValidator.number(this.card.number);
            if (!numberValidation.isValid) {
                this.cardNumberError = 'Card number is invalid';
                this.valid = false;
            }
            // validate expiry month
            var expiryMonthValidation = cardValidator.expirationMonth(this.card.exp_month);
            if (!expiryMonthValidation.isValid) {
                this.expiryMonthError = 'Expiry month is invalid';
                this.valid = false;
            }
            // validate expiry year
            var expiryYearValidation = cardValidator.expirationYear(this.card.exp_year);
            if (!expiryYearValidation.isValid) {
                this.expiryYearError = 'Expiry year is invalid';
                this.valid = false;
            }
            // validate cvc
            var cvcValidation = cardValidator.cvv(this.card.cvc);
            if (!cvcValidation.isValid) {
                this.cvcError = 'CVC is invalid';
                this.valid = false;
            }
 
            if (this.valid) EventBus.$emit('cardIsValid');
        }
    },
    mounted() {
        var cardForm = this;
        // when requested we fire the card validation method
        EventBus.$on('requestCardValidation', function() {
            cardForm.validateCard();
        });
    }
}
</script>
AvoRed.initialize((Vue) => {
    Vue.component('merchant-warrior-card-payment', require('../components/MerchantWarriorCardPayment.vue').default)
    Vue.component('merchant-warrior-card-form', require('../components/CreditCardForm.vue').default)
    Vue.component('merchant-warrior-config', require('../components/MerchantWarriorConfig.vue').default)
})

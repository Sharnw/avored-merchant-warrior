<template>
    <div>
        <a-form-item label="Status">
            <a-select name="mw_card_status" v-model="status">
                <a-select-option value="true">Enabled</a-select-option>
                <a-select-option value="false">Disabled</a-select-option>
            </a-select>
        </a-form-item>
    </div>
</template>

<script>

export default {
    name: 'merchant-warrior-config',
    props: ['data'],
    data () {
        return {
            // whether this payment method is enabled
            status: this.data.hasOwnProperty('mw_card_status') ? this.data.mw_card_status : 'false'
        }
    }
}
</script>

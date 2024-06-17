<template>
   
</template>

<script>
    export default {

        props: {
            showVersion: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            version() {
                return 'v' + this.$appVersion
            },
        }
    }
</script>

<style scoped>

    .version {
        color: #525f7f;
    }
    
</style>

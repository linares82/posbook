<template>
<div v-if="permissions.oscar">
    <a-button type="primary" @click="mostrarPermisos">Oscar</a-button>
</div>


</template>
<script>
import {
    ref
} from 'vue';
export default {

    props: ["permissions"],

    setup(props) {

        let mostrarPermisos = () => {
            console.log('Oscar');
            console.log(props.permissions);
            console.log('Fin Oscar');
        };

        return {
            mostrarPermisos,

        };
    },
};
</script>

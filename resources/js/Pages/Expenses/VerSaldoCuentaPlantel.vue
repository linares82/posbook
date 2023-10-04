<template>
<div>{{ saldo }}</div>
<a-button size="small" @click="consultaSaldo">Ver Saldo...</a-button>
</template>

<script>
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    defineComponent,
    reactive,
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
    PlusOutlined,
    UserOutlined,
    LockOutlined,
    EditOutlined,
    PrinterOutlined,
    SearchOutlined,
    BookOutlined
} from "@ant-design/icons-vue";
import {
    Inertia
} from "@inertiajs/inertia";
import axios from 'axios';
import dayjs from 'dayjs';
import debounce from "lodash/debounce";

export default {

    props: ['plantel', 'account', 'url_consulta_saldo'],

    setup(props) {
        //console.log(props.url_consulta_saldo);
        let saldo = ref();
        const consultaSaldo = () => {
            if (props.plantel == 0 || props.account == 0) {
                alert("La cuenta o el plantel no fue seleccionado.");
            } else {
                axios.get(props.url_consulta_saldo +
                        "?account=" + props.account +
                        "&plantel=" + props.plantel)
                    .then(response => {
                        console.log(response);
                        saldo.value = response.data;
                    });

            }

        };

        return {
            consultaSaldo,
            saldo
        };
    },
}
</script>

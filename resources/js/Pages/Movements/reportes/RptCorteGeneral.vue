<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Caja" sub-title="Entradas Salidas" />
    </a-col>
    <a-col :span="12">

    </a-col>
</a-row>
<a-form ref="formRef" :model="formCashBox" @submit.prevent="submitF" layout="vertical">
    <a-row>

        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item compact label="Fecha de:" name="fecha_f" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formCashBox.fecha_f" format="YYYY-MM-DD HH:mm:ss" :bordered="true" />
                <div v-if="errors.fecha_f">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha_f"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item compact label="Fecha a:" name="fecha_t" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker  v-model:value="formCashBox.fecha_t" format="YYYY-MM-DD HH:mm:ss" :bordered="true" />
                <div v-if="errors.fecha_t">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha_t"></div>
                </div>
            </a-form-item>
        </a-col>

    </a-row>
    <a-row></a-row>
    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processing">Ver Reporte</a-button>
    </a-form-item>

</a-form>
</template>

<script>
import Layout from "../../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    defineComponent,
    reactive,
    computed,
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
    PlusOutlined,
    UserOutlined,
    LockOutlined
} from "@ant-design/icons-vue";
import {
    Inertia
} from "@inertiajs/inertia";
import axios from 'axios';
//import locale from 'ant-design-vue/es/date-picker/locale/es_Es';
/*
import dayjs from 'dayjs';
import 'dayjs/locale/es-mx';
dayjs.locale('es-mx');
*/
export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
        MinusCircleOutlined,
        PlusOutlined,
    },

    props: ["errors"],

    setup(props) {
        const formRef = ref();

        let formCashBox = reactive({
            plantel_id: props.plantel,
            fecha_f: undefined,
            fecha_t: undefined,
        });

        let consultaProductos;

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/movements/corteGeneralR", formCashBox, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };

        //ventana modal
        const loading = ref(false);
        const visible = ref(false);
        const loadingLinea = ref(false);
        const visibleLinea = ref(false);

        return {
            formCashBox,
            submitF,
            processing,
            formRef,
            loading,
            visible,
        };
    },
};
</script>

<style>
    </style>

<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Caja" sub-title="Entradas Salidas" />
    </a-col>
    <a-col :span="12">

    </a-col>
</a-row>
<a-form ref="formRef" :model="formCashBox" @submit.prevent="submitF" layout="vertical">
    <a-row :gutter="20">
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Plantel" name="plantel_id">
                <a-select :options="planteles" mode="multiple" show-search
                    :filter-option="filterOption" v-model:value="formCashBox.plantel_id" style="width: 100%" placeholder="Seleccionar Opción">
                </a-select>
                <div v-if="errors.plantel_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.plantel_id"></div>
                </div>
                <a-button @click="plantelesTodos" size="small">Todos</a-button>
                <a-button @click="plantelesNinguno" size="small">Ninguno</a-button>
            </a-form-item>
        </a-col>
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Cuentas" name="account_id">
                <a-select :options="cuentas" mode="multiple" show-search
                    :filter-option="filterOption" v-model:value="formCashBox.account_id" style="width: 100%" placeholder="Seleccionar Opción">
                </a-select>
                <div v-if="errors.account_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.account_id"></div>
                </div>
                <a-button @click="cuentasTodos" size="small">Todos</a-button>
                <a-button @click="cuentasNinguno" size="small">Ninguno</a-button>
            </a-form-item>
        </a-col>
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Fecha de:" name="fecha_f" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formCashBox.fecha_f" @change="onChange1" format="YYYY-MM-DD" :bordered="true" />
                <div v-if="errors.fecha_f">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha_f"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Fecha a:" name="fecha_t" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formCashBox.fecha_t" @change="onChange2" format="YYYY-MM-DD" :bordered="true" />
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

import dayjs from 'dayjs';
import 'dayjs/locale/es-mx';
dayjs.locale('es-mx');

export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
        MinusCircleOutlined,
        PlusOutlined,
    },

    props: ["errors", "planteles", "cuentas", 'cuentasPluck', 'plantelesPluck'],

    setup(props) {
        const formRef = ref();

        let formCashBox = reactive({
            plantel_id: [],
            account_id:[],
            fecha_f: undefined,
            fecha_t: undefined,
        });



        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/cashBoxes/rptIngresosEgresosR", formCashBox, {
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

        const onChange1 = (value, dateString) => {
            //console.log('Selected Time: ', value);
            //console.log('Formatted Selected Time: ', dateString);
            formCashBox.fecha_f = dayjs(dateString, "YYYY-MM-DD");
        };

        const onChange2 = (value, dateString) => {
            //console.log('Selected Time: ', value);
            //console.log('Formatted Selected Time: ', dateString);
            formCashBox.fecha_t = dayjs(dateString, "YYYY-MM-DD");
        };

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        let plantelesTodos=() => {
            //console.log(props.plantelesPluck);
            props.plantelesPluck.forEach(function(item){
                formCashBox.plantel_id.push(item);
            });
            //console.log(formCashBox.plantel_id);

        };

        const cuentasTodos=() => {
            props.cuentasPluck.forEach(function(item){
                formCashBox.account_id.push(item);
            });
        };

        let plantelesNinguno=() => {
            //console.log(props.plantelesPluck);
            props.plantelesPluck.forEach(function(item){
                formCashBox.plantel_id.shift();
            });
            //console.log(formCashBox.plantel_id);

        };

        const cuentasNinguno=() => {
            props.cuentasPluck.forEach(function(item){
                formCashBox.account_id.shift();
            });
        };

        return {
            formCashBox,
            submitF,
            processing,
            formRef,
            loading,
            visible,
            onChange1,
            onChange2,
            filterOption,
            plantelesTodos,
            cuentasTodos,
            plantelesNinguno,
            cuentasNinguno
        };
    },
};
</script>

<style>
    </style>

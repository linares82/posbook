<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Crear" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderSales" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form ref="formRef" :model="formOrderSale" @submit.prevent="submitF" layout="vertical" @finish="onFinish">
    <a-row>
        <a-col :md="7">
            <a-form-item label="Fecha" name="fecha">
                <a-date-picker v-model:value="formOrderSale.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="7">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formOrderSale.name" :bordered="true" />
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :span="24">
            <a-space v-for="(linea, index) in formOrderSale.lineas" :key="linea.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">
                <a-form-item :name="['linea', index, 'plantel_id']" label="Plantel">
                    <a-select v-model:value="linea.plantel_id" :options="planteles" style="width: 300px" show-search></a-select>
                </a-form-item>

                <a-form-item :name="['linea', index, 'product_id']" label="Producto">
                    <a-select v-model:value="linea.product_id" :options="productos" style="width: 300px" show-search></a-select>
                </a-form-item>

                <a-form-item label="Cantidad" :name="['linea', index, 'cantidad']">
                    <a-input v-model:value="linea.cantidad" style="width: 200px" />
                </a-form-item>

                <MinusCircleOutlined @click="removeLinea(linea)" />
            </a-space>
        </a-col>

        <a-col :span="1"></a-col>
    </a-row>

    <a-form-item>
        <a-button type="dashed" block @click="addLinea">
            <PlusOutlined />
            Agregar Linea
        </a-button>
    </a-form-item>

    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processing">Crear</a-button>
    </a-form-item>
</a-form>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    defineComponent,
    reactive,
    ref,
    watch
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

    props: ["errors", 'productos', 'planteles'],

    setup() {
        const formRef = ref();
        let formOrderSale = reactive({
            fecha: "",
            lineas: []
        });

        let processing = ref(false);

        const removeLinea = item => {
            let index = formOrderSale.lineas.indexOf(item);

            if (index !== -1) {
                formOrderSale.lineas.splice(index, 1);
            }
        };

        const addLinea = () => {
            formOrderSale.lineas.push({
                plantel_id: undefined,
                product_id: undefined,
                cantidad: undefined,
                contacto: undefined,
                tiempo_id: Date.now(),
            });
        };

        const onFinish = values => {
            console.log('Received values of form:', values);
            console.log('formOrderSale:', formOrderSale);
        };

        let submitF = () => {
            Inertia.post("/orderSales/store", formOrderSale, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };
        /*
    const onFinish = (values) => {
      console.log("Success:", values);
    };

    const onFinishFailed = (errorInfo) => {
      console.log("Failed:", errorInfo);
    };
*/

        return {
            formOrderSale,
            submitF,
            processing,
            //dayjs,
            //locale
            formRef,
            onFinish,
            removeLinea,
            addLinea
        };
    },
};
</script>

<style>
</style>

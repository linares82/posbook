<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Productos" sub-title="Crear" />
    </a-col>
    <a-col :span="12">
        <Link href="/products" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form :model="formProduct" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row>
        <a-col :md="7">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formProduct.name"> </a-input>
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item label="Costo" name="costo">
                <a-input v-model:value="formProduct.costo"> </a-input>
                <div v-if="errors.costo">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.costo"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item label="Precio" name="precio">
                <a-input v-model:value="formProduct.precio"> </a-input>
                <div v-if="errors.precio">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.precio"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="7">
            <a-form-item name="bnd_activo">
                <a-checkbox v-model:checked="formProduct.bnd_activo">Activo</a-checkbox>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item name="bnd_ofertable">
                <a-checkbox v-model:checked="formProduct.bnd_ofertable">Ofertable</a-checkbox>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item name="period_id" label="Periodo">
                <a-select v-model:value="formProduct.period_id" show-search placeholder="Seleccionar opción" :options="periods" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item name="product_id" label="Libro">
                <a-select v-model:value="formProduct.product_id" show-search placeholder="Seleccionar opción" :options="books" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item name="cash_box_to_assign_id" label="Caja">
                <a-select v-model:value="formProduct.cash_box_to_assign_id" show-search placeholder="Seleccionar opción" :options="cashBoxes" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

        <a-col :span="1"></a-col>

        <a-col :md="7">
            <a-form-item name="account_id" label="Cuenta Egreso">
                <a-select v-model:value="formProduct.account_id" show-search placeholder="Seleccionar opción" :options="accounts" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

    </a-row>

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
    reactive,
    ref
} from "vue";
import {
    UserOutlined,
    LockOutlined
} from "@ant-design/icons-vue";
import {
    Inertia
} from "@inertiajs/inertia";

export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
    },

    props: ["errors", "periods", "books", 'cashBoxes','accounts'],

    setup() {
        let formProduct = reactive({
            name: "",
            costo: "",
            precio: "",
            bnd_activo: true,
            bnd_ofertable: true,
            period_id: '',
            product_id: "",
            cash_box_to_assign_id: "",
            account_id:""
        });

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/products/store", formProduct, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };

        const filterOption = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };


        return {
            formProduct,
            filterOption,
            submitF,
            processing,
        };
    },
};
</script>

<style>
</style>

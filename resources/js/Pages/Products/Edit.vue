<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Productos" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
        <Link href="/products" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form :model="formProduct" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row :gutter="20">
        <a-col :md="7">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formProduct.name"> </a-input>
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>



        <a-col :md="7">
            <a-form-item label="Costo" name="costo">
                <a-input v-model:value="formProduct.costo"> </a-input>
                <div v-if="errors.costo">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.costo"></div>
                </div>
            </a-form-item>
        </a-col>



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



        <a-col :md="7">
            <a-form-item name="bnd_ofertable">
                <a-checkbox v-model:checked="formProduct.bnd_ofertable">Ofertable</a-checkbox>
            </a-form-item>
        </a-col>



        <a-col :md="7">
            <a-form-item name="period_id" label="Periodo">
                <a-select v-model:value="formProduct.period_id" show-search placeholder="Seleccionar opción" :options="periods" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>



        <a-col :md="7">
            <a-form-item name="product_id" label="Libro">
                <a-select v-model:value="formProduct.product_id" show-search placeholder="Seleccionar opción" :options="books" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

        <a-col :md="7">
            <a-form-item name="exam_id" label="Examen">
                <a-select v-model:value="formProduct.exam_id" show-search placeholder="Seleccionar opción" :options="books" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>



        <a-col :md="7">
            <a-form-item name="cash_box_to_assign_id" label="Caja">
                <a-select v-model:value="formProduct.cash_box_to_assign_id" show-search placeholder="Seleccionar opción" :options="cashBoxes" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>



        <a-col :md="7">
            <a-form-item name="account_id" label="Cuenta Egreso">
                <a-select v-model:value="formProduct.account_id" show-search placeholder="Seleccionar opción" :options="accounts" :filter-option="filterOption"></a-select>
            </a-form-item>
        </a-col>

    </a-row>

    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processing">Actualizar</a-button>
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

    props: ["errors", "product", "periods", "books", "cashBoxes", "accounts"],

    setup(props) {
        let formProduct = reactive({
            id: props.product.id,
            name: props.product.name,
            costo: props.product.costo,
            precio: props.product.precio,
            bnd_activo: props.product.bnd_activo,
            bnd_ofertable: props.product.bnd_ofertable,
            product_id: props.product.product_id,
            exam_id: props.product.exam_id,
            period_id: props.product.period_id,
            cash_box_to_assign_id: props.product.cash_box_to_assign_id,
            account_id: props.product.account_id
        });

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("", formProduct, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };

        const filterOptionPeriods = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        const filterOptionProducts = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        const filterOptionCashBoxes = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        const filterOptionAccounts = (input, option) => {
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        return {
            formProduct,
            submitF,
            processing,
            filterOptionPeriods,
            filterOptionProducts,
            filterOptionCashBoxes,
            filterOptionAccounts
        };
    },
};
</script>

<style>
</style>

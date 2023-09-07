<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Cuentas" sub-title="Crear" />
    </a-col>
    <a-col :span="12">
        <Link href="/accounts" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form :model="formAccount" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row :gutter="20">
        <a-col :md="6">
            <a-form-item label="Codigo" name="code">
                <a-input v-model:value="formAccount.code"> </a-input>
                <div v-if="errors.code">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.code"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="6">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formAccount.name"> </a-input>
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="6">
            <a-form-item label="Ingreso" name="bnd_ingreso">
                <a-checkbox v-model:checked="formAccount.bnd_ingreso"></a-checkbox>
                <div v-if="errors.bnd_ingreso">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_ingreso"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="6">
            <a-form-item label="Egreso" name="bnd_egreso">
                <a-checkbox v-model:checked="formAccount.bnd_egreso"></a-checkbox>
                <div v-if="errors.bnd_egreso">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_egreso"></div>
                </div>
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

    props: ["errors"],

    setup() {
        let formAccount = reactive({
            code: "",
            name: "",
            bnd_ingreso: false,
            bnd_egreso:false
        });

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/accounts/store", formAccount, {
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
            formAccount,
            submitF,
            processing,
        };
    },
};
</script>

<style>
</style>

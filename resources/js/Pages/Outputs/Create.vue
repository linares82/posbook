<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Conceptos Egresos" sub-title="Crear" />
    </a-col>
    <a-col :span="12">
        <Link href="/outputs" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form :model="formOutput" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row :gutter="20">
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formOutput.name"> </a-input>
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">

            <a-checkbox v-model:checked="formOutput.bnd_activo">Activo</a-checkbox>
            <div v-if="errors.bnd_activo">
                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_activo"></div>
            </div>

        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item name="account_id" label="Cuenta Egreso">
                <a-select v-model:value="formOutput.account_id" show-search placeholder="Seleccionar opción" :options="accounts" :filter-option="filterOpttionsAccounts"></a-select>
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

    props: ["errors", "accounts"],

    setup() {
        let formOutput = reactive({
            name: "",
            account_id: "",
            bnd_activo: "",
        });

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/outputs/store", formOutput, {
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
            formOutput,
            submitF,
            processing,
        };
    },
};
</script>

<style>
</style>

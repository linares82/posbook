<template>
<a-row>
    <a-col :md="8"></a-col>
    <a-col :md="8">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Login" sub-title="Captura tu usario y contraseña" />
    </a-col>
    <a-col :md="8"></a-col>
</a-row>
<a-form :model="formUser" @submit.prevent="submitF" autocomplete="on" layout="vertical">
    <a-row>
        <a-col :md="8">

        </a-col>
        <a-col :md="8">
            <a-form-item label="Email" name="email">
                <a-input v-model:value="formUser.email"> </a-input>
                <div v-if="errors.email">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.email"></div>
                </div>
            </a-form-item>

            <a-form-item label="Password" name="password">
                <a-input-password v-model:value="formUser.password">
                </a-input-password>
                <div v-if="errors.password">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.password"></div>
                </div>
            </a-form-item>
            <a-form-item name="remember" :wrapper-col="{ offset: 8, span: 16 }">
                <a-checkbox v-model:checked="formUser.remember">Recuerda me</a-checkbox>
            </a-form-item>
            <a-form-item>
                <a-button type="primary" html-type="submit" :disabled="processing">Crear</a-button>
            </a-form-item>

            <div v-if="$page.props.errors.sysMessage">
                <div role="alert" class="ant-form-item-explain-error" style="" v-text="$page.props.errors.sysMessage"></div>
            </div>
        </a-col>

        <a-col :md="8"> </a-col>
    </a-row>
</a-form>
</template>

<script>
import LoginLayout from "../../shared/LoginLayout";
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
    layout: LoginLayout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
    },

    props: ["sysMessage", "errors"],

    setup(props) {

        let formUser = reactive({
            password: "",
            email: "",
            remember: ref(true)
        });

        let processing = ref(false);

        let submitF = () => {
            Inertia.post("/authenticate", formUser, {
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
            formUser,
            submitF,
            processing,
        };
    },
};
</script>

<style>
</style>

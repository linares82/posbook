<template>
  <a-row>
    <a-col :span="12">
      <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        title="Usuarios"
        sub-title="Editar"
      />
    </a-col>
    <a-col :span="12">
      <Link
        href="/users"
        class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm"
        as="button"
        >Regresar</Link
      >
    </a-col>
  </a-row>
  <a-form
    :model="formUser"
    @submit.prevent="submitF"
    autocomplete="off"
    layout="vertical"
  >
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="name">
          <a-input v-model:value="formUser.name"> </a-input>
          <div v-if="errors.name">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.name"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Email" name="email">
          <a-input v-model:value="formUser.email"> </a-input>
          <div v-if="errors.email">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.email"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :span="7">
        <a-form-item label="Password" name="password">
          <a-input-password v-model:value="formUser.password">
          </a-input-password>
          <div v-if="errors.password">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.password"
            ></div>
          </div>
        </a-form-item>
      </a-col>
    </a-row>

    <a-form-item>
      <a-button type="primary" html-type="submit" :disabled="processing"
        >Actualizar</a-button
      >
    </a-form-item>
  </a-form>
</template>
<script>
import Layout from "../../shared/Layout";
import { Link } from "@inertiajs/inertia-vue3";
import { reactive, ref } from "vue";
import { UserOutlined, LockOutlined } from "@ant-design/icons-vue";
import { Inertia } from "@inertiajs/inertia";


export default {
  layout: Layout,
  components: {
    Link,
    UserOutlined,
    LockOutlined,
  },

  props: ["errors","user"],

  setup(props) {
    let formUser = reactive({
      id:props.user.id,
      name: props.user.name,
      password: "",
      email: props.user.email,
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("", formUser, { 
        onStart: () => {
          processing.value = true;
        },
        onFinish: () => {
          processing.value = false;
        },
      });
    };
    

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

<template>
  <a-row>
    <a-col :span="12">
      <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        title="Menus"
        sub-title="Crear"
      />
    </a-col>
    <a-col :span="12">
      <Link
        href="/menus"
        class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm"
        as="button"
        >Regresar</Link
      >
    </a-col>
  </a-row>
  <a-form
    :model="formMenu"
    @submit.prevent="submitF"
    autocomplete="off"
    layout="vertical"
  >
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="item">
          <a-input v-model:value="formMenu.item"> </a-input>
          <div v-if="errors.item">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.item"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Orden" name="orden">
          <a-input v-model:value="formMenu.orden"> </a-input>
          <div v-if="errors.orden">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.orden"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Depende de" name="depende_de">
          <a-input v-model:value="formMenu.depende_de"> </a-input>
          <div v-if="errors.depende_de">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.depende_de"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Link" name="link">
          <a-input v-model:value="formMenu.link"> </a-input>
          <div v-if="errors.link">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.link"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Permiso" name="permiso">
          <a-input v-model:value="formMenu.permiso"> </a-input>
          <div v-if="errors.permiso">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.permiso"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Target" name="target">
          <a-input v-model:value="formMenu.target"> </a-input>
          <div v-if="errors.target">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.target"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Imagen" name="imagen">
          <a-input v-model:value="formMenu.imagen"> </a-input>
          <div v-if="errors.imagen">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.imagen"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>
    </a-row>

    
      
    <a-form-item>
      <a-button type="primary" html-type="submit" :disabled="processing"
        >Crear</a-button
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

  props: ["errors"],

  setup() {
    let formMenu = reactive({
      item: "",
      orden: "",
      depende_de:"",
      link:"",
      permiso:"",
      target:"",
      imagen:""
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("/menus/store", formMenu, {
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
      formMenu,
      submitF,
      processing,
    };
  },
};
</script>
<style>
</style>

<template>
  <a-row>
    <a-col :span="12">
      <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        title="O. Devoluciones"
        sub-title="Crear"
      />
    </a-col>
    <a-col :span="12">
      <Link
        href="/orderDevolutions"
        class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm"
        as="button"
        >Regresar</Link
      >
    </a-col>
  </a-row>
  <a-form
    :model="formOrderDevolution"
    @submit.prevent="submitF"
    autocomplete="off"
    layout="vertical"
  >
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="name">
          <a-input v-model:value="formOrderDevolution.name"> </a-input>
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
            <a-form-item label="Fecha" name="fecha">
                <a-date-picker v-model:value="formOrderDevolution.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7"> 
        <a-form-item label="Motivo" name="motivo">
          <a-input v-model:value="formOrderDevolution.motivo"> </a-input>
          <div v-if="errors.motivo">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.motivo"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>


      <a-col :span="1"></a-col>


      <a-col :span="1"></a-col>


      <a-col :span="1"></a-col>


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
    let formOrderDevolution = reactive({
      name: "",
      fecha: "",
      motivo:""
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("/orderDevolutions/store", formOrderDevolution, {
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
      formOrderDevolution,
      submitF,
      processing,
    };
  },
};
</script>
<style>
</style>

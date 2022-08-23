<template>
  <a-row>
    <a-col :span="12">
      <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Planteles" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
      <Link href="/plantels" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
  </a-row>
  <a-form :model="formPlantel" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="name">
          <a-input v-model:value="formPlantel.name"> </a-input>
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
        <a-form-item label="Dirección" name="address">
          <a-input v-model:value="formPlantel.address"> </a-input>
          <div v-if="errors.address">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.address"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Teléfono" name="phone">
          <a-input v-model:value="formPlantel.phone"> </a-input>
          <div v-if="errors.phone">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.phone"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Director" name="director">
          <a-input v-model:value="formPlantel.director"> </a-input>
          <div v-if="errors.director">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.director"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :span="1"></a-col>

      <a-col :span="1"></a-col>

      <a-col :span="1"></a-col>
    </a-row>

    <a-form-item>
      <a-button type="primary" html-type="submit" :disabled="processing">Actualizar</a-button>
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

  props: ["errors", "plantel"],

  setup(props) {
    let formPlantel = reactive({
      id: props.plantel.id,
      name: props.plantel.name,
      address: props.plantel.address,
      phone: props.plantel.phone,
      director: props.plantel.director,
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("", formPlantel, {
        onStart: () => {
          processing.value = true;
        },
        onFinish: () => {
          processing.value = false;
        },
      });
    };


    return {
      formPlantel,
      submitF,
      processing,
    };
  },
};
</script>
<style>
</style>

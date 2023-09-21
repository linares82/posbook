<template>
  <a-row>
    <a-col :span="12">
      <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Metodos Pago" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
      <Link href="/paymentMethods" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
  </a-row>
  <a-form :model="formPaymentMethod" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="name">
          <a-input v-model:value="formPaymentMethod.name"> </a-input>
          <div v-if="errors.name">
            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.item"></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Excento" name="bnd_exempt">
          <a-checkbox v-model:checked="formPaymentMethod.bnd_exempt"></a-checkbox>
          <div v-if="errors.bnd_exempt">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.bnd_exempt"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="% Descuento" name="porcentaje_descuento">
          <a-input v-model:value="formPaymentMethod.porcentaje_descuento"> </a-input>
          <div v-if="errors.porcentaje_descuento">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.porcentaje_descuento"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
            <a-form-item name="account_id" label="Cuenta Ingreso">
                <a-select v-model:value="formPaymentMethod.account_id" show-search placeholder="Seleccionar opción" :options="accounts" :filter-option="filterOption"></a-select>
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

  props: ["errors", "paymentMethod", 'accounts'],

  setup(props) {
    let formPaymentMethod = reactive({
      id: props.paymentMethod.id,
      name: props.paymentMethod.name,
      bnd_exempt: props.paymentMethod.bnd_exempt,
      porcentaje_descuento:props.paymentMethod.porcentaje_descuento,
      account_id:props.paymentMethod.account_id
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("", formPaymentMethod, {
        onStart: () => {
          processing.value = true;
        },
        onFinish: () => {
          processing.value = false;
        },
      });
    };

    const filterOption = (input, option) => {
            //console.log(option);
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };


    return {
      formPaymentMethod,
      submitF,
      processing,
      filterOption
    };
  },
};
</script>
<style>
</style>

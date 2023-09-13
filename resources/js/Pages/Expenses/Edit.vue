<template>
  <a-row>
    <a-col :span="12">
      <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Egresos" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
      <Link href="/expenses" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
  </a-row>
  <a-form :model="formExpense" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row :gutter="20">
        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Plantel" name="plantel_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-select :options="planteles" show-search v-model:value="formExpense.plantel_id" style="width: 300px" placeholder="Seleccionar Opción">
                </a-select>
                <div v-if="errors.plantel_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.plantel_id"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Concepto" name="output_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-select :options="outputs" show-search v-model:value="formExpense.output_id" style="width: 300px" placeholder="Seleccionar Opción">
                </a-select>
                <div v-if="errors.output_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.output_id"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item compact label="Fecha" name="fecha" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formExpense.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <a-form-item label="Detalle" name="detalle">
                <a-input v-model:value="formExpense.detalle"> </a-input>
                <div v-if="errors.detalle">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.detalle"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
            <a-form-item label="Monto" name="monto">
                <a-input v-model:value="formExpense.monto"> </a-input>
                <div v-if="errors.monto">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.monto"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <a-form-item label="Observaciones" name="observaciones">
                <a-textarea v-model:value="formExpense.observaciones"> </a-textarea>
                <div v-if="errors.observaciones">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.observaciones"></div>
                </div>
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
import { Link } from "@inertiajs/inertia-vue3";
import { reactive, ref } from "vue";
import { UserOutlined, LockOutlined } from "@ant-design/icons-vue";
import { Inertia } from "@inertiajs/inertia";
import dayjs from "dayjs";

export default {
  layout: Layout,
  components: {
    Link,
    UserOutlined,
    LockOutlined,
  },

  props: ["errors", "expense","outputs","planteles"],

  setup(props) {
    let formExpense = reactive({
      id: props.expense.id,
      plantel_id: props.expense.plantel_id,
      output_id: props.expense.output_id,
      detalle: props.expense.detalle,
      fecha: dayjs(props.expense.fecha, "YYYY-MM-DD"),
      monto: props.expense.monto,
      observaciones: props.expense.observaciones
    });

    let processing = ref(false);

    let submitF = () => {
      Inertia.post("", formExpense, {
        onStart: () => {
          processing.value = true;
        },
        onFinish: () => {
          processing.value = false;
        },
      });
    };


    return {
      formExpense,
      submitF,
      processing,
    };
  },
};
</script>
<style>
</style>

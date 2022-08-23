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
    <a-row>
      <a-col :md="7">
        <a-form-item label="Nombre" name="name">
          <a-input v-model:value="formProduct.name"> </a-input>
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
        <a-form-item label="Costo" name="costo">
          <a-input v-model:value="formProduct.costo"> </a-input>
          <div v-if="errors.costo">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.costo"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item label="Precio" name="precio">
          <a-input v-model:value="formProduct.precio"> </a-input>
          <div v-if="errors.precio">
            <div
              role="alert"
              class="ant-form-item-explain-error"
              style=""
              v-text="errors.precio"
            ></div>
          </div>
        </a-form-item>
      </a-col>

      <a-col :md="7">
        <a-form-item name="bnd_activo" >
        <a-checkbox v-model:checked="formProduct.bnd_activo">Activo</a-checkbox>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item name="bnd_ofertable" >
        <a-checkbox v-model:checked="formProduct.bnd_ofertable">Ofertable</a-checkbox>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item name="period_id" label="Periodo" >
        <a-select
          v-model:value="formProduct.period_id"
          show-search
          placeholder="Seleccionar opción"
          :options="periods"
          :filter-option="filterOptionPeriods"
  ></a-select>
        </a-form-item>
      </a-col>

      <a-col :span="1"></a-col>

      <a-col :md="7">
        <a-form-item name="product_id" label="Libro" >
        <a-select
          v-model:value="formProduct.product_id"
          show-search
          placeholder="Seleccionar opción"
          :options="books"
          :filter-option="filterOptionProducts"
        ></a-select>
        </a-form-item>
      </a-col>

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

  props: ["errors", "product", "periods", "books"],

  setup(props) {
    let formProduct = reactive({
      id: props.product.id,
      name: props.product.name,
      costo: props.product.costo,
      precio:props.product.precio,
      bnd_activo: props.product.bnd_activo,
      bnd_ofertable:props.product.bnd_ofertable,
      product_id: props.product.product_id,
      period_id:props.product.period_id
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

    return {
      formProduct,
      submitF,
      processing,
      filterOptionPeriods,
      filterOptionProducts,
    };
  },
};
</script>
<style>
</style>

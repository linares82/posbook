<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Estatus Pagos" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/stPayments" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
  <a-col :span="24">
    <a-descriptions title="Información" layout="vertical">
      <a-descriptions-item label="Id">{{stPayment.id}}</a-descriptions-item>
      <a-descriptions-item label="Nombre">{{stPayment.name}}</a-descriptions-item>
    </a-descriptions>
  </a-col>
</a-row>


</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";

export default {
  layout: Layout,

  components: {
      Link
  },

  props: ["stPayment"],

};
</script>

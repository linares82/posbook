<template>
  <a-row>
    <a-col :span="12">
      <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Usuarios" sub-title="ver" />
    </a-col>
    <a-col :span="12">
      <Link href="/users" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
  </a-row>

  <a-descriptions title="Información" layout="vertical">
    <a-descriptions-item label="Id">{{ user.id }}</a-descriptions-item>
    <a-descriptions-item label="Nombre">{{ user.name }}</a-descriptions-item>
    <a-descriptions-item label="Email">{{ user.email }}</a-descriptions-item>
  </a-descriptions>

  <a-row>
    <a-col :span="11">
      <strong>Seleccionar Roles</strong>
      <a-transfer :data-source="roles" v-model:target-keys="targetKeys" :titles="[' - Libres', ' - Seleccionados']"
        show-search @change="handleChange" :list-style="{
          width: '400px',
          height: '500px',
        }" :operations="['Derecha', 'Izquierda']" :render="item => `${item.title}`">
        <template #notFoundContent>
          <span>Sin información</span>
        </template>
      </a-transfer>
    </a-col>
    <a-col :span="1"></a-col>
    <a-col :span="11">
      <strong>Seleccionar Planteles</strong>
      <a-transfer :data-source="planteles" v-model:target-keys="targetKeysP" :titles="[' - Libres', ' - Seleccionados']"
        show-search @change="handleChangeP" :list-style="{
          width: '400px',
          height: '500px',
        }" :operations="['Derecha', 'Izquierda']" :render="item => `${item.title}`">
        <template #notFoundContent>
          <span>Sin información</span>
        </template>
      </a-transfer>
    </a-col>
    <a-col :span="1"></a-col>
  </a-row>
</template>

<script>
import Layout from "../../shared/Layout";
import { defineComponent, ref } from 'vue';
import {
  Link
} from "@inertiajs/inertia-vue3";
import {
  Inertia
} from "@inertiajs/inertia";

export default {
  layout: Layout,

  components: {
    Link
  },

  props: ["user", "roles", "selectedRoles", 'planteles', 'selectedPlanteles'],

  setup(props) {
    
    let selected = [];
    Object.entries(props.selectedRoles).forEach(([key, value]) => {
      selected.push(parseInt(value))
    });
    const targetKeys = ref(selected);

    const targetKeysP = ref(props.selectedPlanteles);
    console.log(targetKeysP)
    
    //console.log(props.selectedPlanteles);
    //console.log(props.selectedPermissions);

    //console.log(targetKeys);
    const handleChange = (nextTargetKeys, direction, moveKeys) => {
      //console.log('targetKeys: ', nextTargetKeys);
      Inertia.post(
        "/users/assignRolesToAUser", {
        roles: nextTargetKeys,
        user: props.user.id
      }, {
        preserveState: true,
        replace: true,
      }
      );
    };

    const handleChangeP = (nextTargetKeysP, direction, moveKeys) => {
      Inertia.post(
        "/users/assignPlantelsToAUser", {
        plantels: nextTargetKeysP,
        user: props.user.id
      }, {
        preserveState: true,
        replace: true,
      }
      );
    };

    return {
      handleChange,
      handleChangeP,
      targetKeys,
      targetKeysP
    };
  },
};
</script>

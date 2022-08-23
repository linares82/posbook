<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Roles" sub-title="Asignar Permisos" />
    </a-col>
    <a-col :span="12">
        <Link href="/roles" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
  <a-col :span="12">
    <a-descriptions title="Información" layout="vertical">
      <a-descriptions-item label="Id">{{role.id}}</a-descriptions-item>
      <a-descriptions-item label="Nombre">{{role.name}}</a-descriptions-item>
    </a-descriptions>
  </a-col>
</a-row>

<a-row>
  <a-col :span="12">
  <strong>Seleccionar Permisos</strong>
<a-transfer
    
    :data-source="permissions"
    v-model:target-keys="targetKeys"
    :titles="[' - Libres', ' - Seleccionados']"
    show-search
    @change="handleChange"
    :list-style="{
      width: '400px',
      height: '500px',
    }"
    :operations="['Derecha', 'Izquierda']"
    :render="item => `${item.title}`"
  >
    <template #notFoundContent>
      <span>Sin información</span>
    </template>
  </a-transfer>
  </a-col>
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
import { message } from 'ant-design-vue';

export default defineComponent({
    layout: Layout,

    components: {
        Link
    },

    props: ["role","permissions","selectedPermissions","sysMessage"],

    setup(props) {
    //data(props){
        mounted: {
            
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };
        /*
        let selected=[];
        Object.entries(props.selectedPermissions).forEach(([key, value]) => {
          selected.push(parseInt(value))
        });*/
        //console.log(selected)
        const targetKeys = ref(props.selectedPermissions);
        //console.log(targetKeys);
        //console.log(props.selectedPermissions);
        
        //console.log(targetKeys);
        const handleChange = (nextTargetKeys, direction, moveKeys) => {
          console.log('targetKeys: ', nextTargetKeys);
          Inertia.post(
                    "/roles/assignPermissionsToARole", {
                        permissions: nextTargetKeys,
                        role : props.role.id
                    }, {
                        preserveState: true,
                        replace: true,
                    }
                );
        };

        return {
          handleChange,
          targetKeys
        };
    },
});
</script>

<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Roles" sub-title="Lista" />
    </a-col>
    <a-col :span="12">
        <div v-if="permissions.rolesCreate">
            <Link  href="/roles/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear</Link>
        </div>
        
        <a-alert showIcon type="success" v-if="sysMessage != ''" :message="sysMessage" closable>
            <template #icon>
                <smile-outlined />
            </template>
        </a-alert>
    </a-col>
</a-row>

<a-collapse>
    <a-collapse-panel key="1" header="Buscar">
        <a-row>
            <a-col :span="6">
                <a-input v-model:value="roleName" size="small" placeholder="Buscar Rol">
                    <template #prefix>
                        <search-outlined />
                    </template>
                    <template #suffix>
                        <a-tooltip title="Buscar Rol">
                            <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                        </a-tooltip>
                    </template>
                </a-input>
            </a-col>
        </a-row>
    </a-collapse-panel>
</a-collapse>

<div class="ant-table-wrapper ant-table-striped">
    <div class="ant-spin-nested-loading">
        <div class="ant-spin-container">
            <div class="ant-table ant-table-small ant-table-bordered">
                <!---->
                <div class="ant-table-container">
                    <div class="ant-table-content">
                        <table style="table-layout: auto;" class="ant-table-striped">
                            <colgroup></colgroup>
                            <thead class="ant-table-thead">
                                <th class="ant-table-cell" colstart="0" colend="0">Id</th>
                                <th class="ant-table-cell" colstart="1" colend="1">Nombre</th>
                                <th class="ant-table-cell" colstart="3" colend="3">Acciones</th>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr class="ant-table-row ant-table-row-level-0" v-for="role in roles.data" :key="role.id">
                                    <td class="ant-table-cell" colstart="0" colend="0">{{ role.id }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ role.name }}</td>
                                    <td class="ant-table-cell" colstart="3" colend="3">
                                        <a-dropdown-button >
                                            Acciones
                                            <template #overlay>
                                                <a-menu>
                                                    <a-menu-item key="1" v-if="permissions.rolesEdit">
                                                        <form-outlined />
                                                        <Link :href="`/roles/edit/${role.id}`" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm" as="button">Editar</Link>
                                                    </a-menu-item>
                                                    <a-menu-item key="2" v-if="permissions.rolesDestroy">
                                                        <delete-outlined />
                                                        <button @click="borrar(role.id)" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                    </a-menu-item>
                                                    <a-menu-item key="3" v-if="permissions.rolesShow">
                                                        <eye-outlined />
                                                        <Link :href="`/roles/show/${role.id}`" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm" as="button">Asignar Permisos</Link>
                                                    </a-menu-item>
                                                </a-menu>
                                            </template>
                                        </a-dropdown-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<paginator :datos="roles"> </paginator>
</template>

<script>
import Layout from "../../shared/Layout";
import Paginator from "../../shared/Paginator";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    SmileOutlined,
    SearchOutlined,
    InfoCircleOutlined,
    FormOutlined,
    DeleteOutlined,
    EyeOutlined
} from "@ant-design/icons-vue";
import {
    watch,
    ref
} from "vue";
import {
    Inertia
} from "@inertiajs/inertia";
import debounce from "lodash/debounce";

export default {
    layout: Layout,

    components: {
        Link,
        Paginator,
        SearchOutlined,
        InfoCircleOutlined,
        SmileOutlined,
        FormOutlined,
        DeleteOutlined,
        EyeOutlined
    },

    props: ["roles", "filters", "sysMessage",'permissions'],

    setup(props) {
        const borrar = (id) => {
            if (confirm("estas seguro?")) {
                Inertia.delete(`/roles/delete/${id}`)
            }
        };
        console.log(props.permissions);
        let roleName = ref(props.filters.roleName);
        watch(
            roleName,
            debounce(function (value) {
                Inertia.get(
                    "/roles", {
                        roleName: value
                    }, {
                        preserveState: true,
                        replace: true,
                    }
                );
            }, 500)
        );
        return {
            borrar,
            roleName,
            paginator: false,
            
        };
    },
};
</script>

<style scoped>
</style>

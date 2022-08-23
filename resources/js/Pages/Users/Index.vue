<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Usuarios" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <div v-if="permissions.usersCreate">
                <Link href="/users/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
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
                    <a-input v-model:value="userName" size="small" placeholder="Buscar Usuario">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar Usuario">
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
                                    <th class="ant-table-cell" colstart="2" colend="2">Email</th>
                                    <th class="ant-table-cell" colstart="2" colend="3">Roles</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="usuario in usuarios.data"
                                        :key="usuario.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ usuario.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ usuario.name }}</td>
                                        <td class="ant-table-cell" colstart="2" colend="2">{{ usuario.email }}</td>
                                        <td class="ant-table-cell" colstart="2" colend="3">
                                            <span v-for="role in usuario.roles" :key="role.id">
                                                <Link :href="`/roles/show/${role.id}`"
                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                    as="button">
                                                {{ role.name }}
                                                </Link>

                                            </span>

                                        </td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.usersEdit">
                                                            <form-outlined />
                                                            <a-popconfirm title="Are you sure delete this task?"
                                                                ok-text="Yes" cancel-text="No" @confirm="confirm"
                                                                @cancel="cancel">
                                                                <Link :href="`/users/edit/${usuario.id}`"
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                    as="button">Editar</Link>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.usersDestroy">
                                                            <delete-outlined />
                                                            <button @click="borrar(usuario.id)"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.usersShow">
                                                            <eye-outlined />
                                                            <Link :href="`/users/show/${usuario.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Asignar Roles y Planteles</Link>
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

    <paginator :datos="users"> </paginator>
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

    props: ["users", "filters", "sysMessage", "usuarios", "permissions"],

    setup(props) {
        const borrar = (id) => {
            if (confirm("estas seguro?")) {
                Inertia.delete(`/users/delete/${id}`)
            }
        };
        let userName = ref(props.filters.userName);
        watch(
            userName,
            debounce(function (value) {
                Inertia.get(
                    "/users", {
                    userName: value
                }, {
                    preserveState: true,
                    replace: true,
                }
                );
            }, 500)
        );
        return {
            borrar,
            userName,
            paginator: false,

        };
    },
};
</script>

<style scoped>
</style>

<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Cortes" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <!--
            <div v-if="permissions.cortesCreate">
                <Link href="/cortes/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
            </div>
            -->

        </a-col>
    </a-row>

    <a-collapse>
        <a-collapse-panel key="1" header="Buscar">
            <a-row>
                <a-col :span="6">
                    <a-input v-model:value="name" size="small" placeholder="Buscar Corte">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar Corte">
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
                                    <th class="ant-table-cell" colstart="1" colend="1">Cuenta</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Plantel</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">F. Inicio</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">F. Fin</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Saldo Ingresos</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Saldo Egresos</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="corte in cortes.data"
                                        :key="corte.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ corte.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.account }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.plantel }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.fecha_inicio }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.fecha_fin }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.saldo_ingresos }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ corte.saldo_egresos }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <!--
                                                        <a-menu-item key="1" v-if="permissions.cortesEdit">
                                                            <form-outlined />
                                                            <Link :href="`/cortes/edit/${corte.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        -->
                                                        <a-menu-item key="2" v-if="permissions.cortesDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(corte.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.cortesShow">
                                                            <eye-outlined />

                                                            <Link :href="`/cortes/show/${corte.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Ver</Link>

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

    <paginator :datos="cortes"> </paginator>
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
import { message } from 'ant-design-vue';
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

    props: ["cortes", "filters", "sysMessage", 'permissions'],

    setup(props) {
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/cortes/delete/${id}`)
            //}
        };

        let name = ref(props.filters.name);
        watch(
            name,
            debounce(function (value) {
                Inertia.get(
                    "/cortes", {
                    name: value
                }, {
                    preserveState: true,
                    replace: true,
                }
                );
            }, 500)
        );
        return {
            borrar,
            name,
            paginator: false,

        };
    },
};
</script>

<style scoped>
</style>

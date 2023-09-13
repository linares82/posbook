<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Egresos" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <div v-if="permissions.expensesCreate">
                <Link href="/expenses/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
            </div>


        </a-col>
    </a-row>

    <a-collapse>
        <a-collapse-panel key="1" header="Buscar">
            <a-row>
                <a-col :span="6">
                    <a-input v-model:value="name" size="small" placeholder="Buscar Egresos">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar Egreso">
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
                                    <th class="ant-table-cell" colstart="1" colend="1">Plantel</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Egreso</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Detalle</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Fecha</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Monto</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="expense in expenses.data"
                                        :key="expense.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ expense.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ expense.plantel }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ expense.output }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ expense.detalle }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ expense.fecha }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ expense.monto }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.expensesEdit">
                                                            <form-outlined />
                                                            <Link :href="`/expenses/edit/${expense.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.expensesDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(expense.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.expensesShow">
                                                            <eye-outlined />

                                                            <Link :href="`/expenses/show/${expense.id}`"
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

    <paginator :datos="expenses"> </paginator>
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

    props: ["expenses", "filters", "sysMessage", 'permissions'],

    setup(props) {
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/expenses/delete/${id}`)
            //}
        };

        let name = ref(props.filters.name);
        watch(
            name,
            debounce(function (value) {
                Inertia.get(
                    "/expenses", {
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

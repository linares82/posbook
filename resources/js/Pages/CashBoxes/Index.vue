<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Caja" sub-title="Lista" />
    </a-col>
    <a-col :span="12">
        <div v-if="permissions.cashBoxesCreate">
            <Link href="/cashBoxes/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
            </Link>
        </div>
        <PermIdentificados :permissions="permissions"></PermIdentificados>
    </a-col>
</a-row>

<a-collapse>
    <a-collapse-panel key="1" header="Buscar">
        <a-row>
            <a-col :span="6">
                    <a-input v-model:value="search.id" size="small" placeholder="Buscar id...">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar Id...">
                                <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                            </a-tooltip>
                        </template>
                    </a-input>
                </a-col>
            <a-col :span="6">
                    <a-select
                    :options="planteles"
                    v-model:value="search.plantel_id"
                    style="width: 200px"
                    placeholder="Seleccionar Plantel..."
                    show-search
                    :filter-option="filterOption"
                    >
                    </a-select>
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
                                <th class="ant-table-cell" colstart="1" colend="1">Cliente</th>
                                <th class="ant-table-cell" colstart="1" colend="1">Fecha</th>
                                <th class="ant-table-cell" colstart="1" colend="1">Total</th>
                                <th class="ant-table-cell" colstart="1" colend="1">Estatus</th>
                                <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr class="ant-table-row ant-table-row-level-0" v-for="cashBox in cashBoxes.data" :key="cashBox.id">
                                    <td class="ant-table-cell" colstart="0" colend="0">{{ cashBox.id }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ cashBox.plantel }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ cashBox.cliente }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ cashBox.fecha }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ cashBox.total }}</td>
                                    <td class="ant-table-cell" colstart="1" colend="1">{{ cashBox.estatus }}</td>
                                    <td class="ant-table-cell" colstart="3" colend="4">
                                        <a-dropdown-button>
                                            Acciones
                                            <template #overlay>
                                                <a-menu>
                                                    <a-menu-item key="1" v-if="permissions.cashBoxesEdit && cashBox.st_cash_box_id!==4">
                                                        <form-outlined />
                                                        <Link :href="`/cashBoxes/edit/${cashBox.id}`" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm" as="button">Editar</Link>
                                                    </a-menu-item>
                                                    <!--<a-menu-item key="2" v-if="permissions.cashBoxesDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(cashBox.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>

                                                    </a-menu-item>
                                                    -->
                                                    <a-menu-item key="3" v-if="permissions.cashBoxesCancelCashBox">
                                                        <delete-outlined />

                                                        <Link :href="`/cashBoxes/cancelCashBox/${cashBox.id}`" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm" as="button">Cancelar</Link>

                                                    </a-menu-item>
                                                    <a-menu-item key="4" v-if="permissions.cashBoxesShow">
                                                        <eye-outlined />

                                                        <Link :href="`/cashBoxes/show/${cashBox.id}`" class="ant-btn ant-btn-default ant-btn-round ant-btn-sm" as="button">Ver</Link>

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

<paginator :datos="cashBoxes"> </paginator>
</template>

<script>
import Layout from "../../shared/Layout";
import permIdentificados from "../../shared/permIdentificados";
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
    message
} from 'ant-design-vue';
import {
    watch,
    ref,
    reactive
} from "vue";
import {
    Inertia
} from "@inertiajs/inertia";
import debounce from "lodash/debounce";
import PermIdentificados from "../../shared/permIdentificados.vue";

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
    EyeOutlined,
    permIdentificados,
    PermIdentificados
},

    props: ["cashBoxes", "filters", "sysMessage", 'permissions', 'planteles'],

    setup(props) {
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/cashBoxes/delete/${id}`)
            //}
        };

        //let name = ref(props.filters.name);
        let search = reactive({
            plantel_id : props.filters.plantel_id,
            id: props.filters.id,
            //name : props.filters.name,
            column: props.filters.column,
            direction: props.filters.direction
        });
        watch(
            search,
            debounce(function (value) {
                Inertia.get(
                    "/cashBoxes", {
                        search: value
                    }, {
                        preserveState: true,
                        replace: true,
                    }
                );
            }, 500)
        );

        const filterOption = (input, option) => {
            //console.log(option);
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        return {
            borrar,
            search,
            paginator: false,
            filterOption
        };
    },
};
</script>

<style scoped>
</style>

<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Cuentas" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/accounts" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{account.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{account.name}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>

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
                                    <th class="ant-table-cell" colstart="1" colend="1">Cuenta</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Fecha Inicio</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Saldo Ingreso</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Saldo Egreso</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Diferencia Calculada</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="accountPlantel in accountPlantels"
                                        :key="accountPlantel.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ accountPlantel.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.plantel }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.account }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.fecha_inicio }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.saldo_ingresos }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.saldo_egresos }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ accountPlantel.saldo_ingresos-accountPlantel.saldo_egresos }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.accountPlantelsEdit">
                                                            <form-outlined />
                                                            <Link :href="`/accountPlantels/edit/${accountPlantel.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.accountPlantelsDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(accountPlantel.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.accountPlantelsShow">
                                                            <eye-outlined />

                                                            <Link :href="`/accountPlantels/show/${accountPlantel.id}`"
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

    props: ["account","accountPlantels"],

    setup(props){
        //console.log(props.accountPlantels);
    }

};
</script>

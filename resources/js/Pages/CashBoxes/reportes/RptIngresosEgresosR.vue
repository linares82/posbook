<template>
<a-row>
    <a-col :span="24" style="background-color: #fff;">
        <div class="ant-table-wrapper ant-table-striped">
            <div class="ant-spin-nested-loading">
                <div class="ant-spin-container">
                    <div class="ant-table ant-table-small ant-table-bordered">
                        <!---->
                        <div class="ant-table-container">
                            <div class="ant-table-content">
                                <a-row type="flex" justify="center" align="top">
                                    <h3>Ingresos y Egresos</h3>
                                </a-row>

                                <a-row type="flex" justify="center" align="top">
                                    <h4> Del {{ fecha1 }} Al {{ fecha2 }}</h4>
                                </a-row>

                                <table style="table-layout: auto;" class="ant-table-striped">
                                    <colgroup></colgroup>
                                    <thead class="ant-table-thead">
                                        <th>Plantel</th>
                                        <th>Cuenta</th>
                                        <th>Producto</th>
                                        <th>Entregado</th>
                                        <th>Estatus Pago</th>
                                        <th>Caja</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                    </thead>
                                    <tbody class="ant-table-tbody">
                                        <tr class="ant-table-row ant-table-row-level-0" v-for="ingreso in ingresos" :key="ingreso.cash_box_id">
                                            <td class="ant-table-cell" colstart="0" colend="0">{{ ingreso.plantel }}</td>
                                            <td class="ant-table-cell column-money" colstart="0" colend="0">
                                                {{ ingreso.codigo }} {{ ingreso.cuenta }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ ingreso.producto  }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ ingreso.bnd_entregado ? 'Si' : 'No' }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ ingreso.estatus_pago }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ ingreso.cash_box_id }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ ingreso.fecha }}
                                            </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{
                                                    Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(ingreso.monto)
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">Total Ingresos</td>
                                            <td align="right">
                                                {{
                                            Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(total_ingresos)
                                            }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a-col>
    <a-col :span="24" style="background-color: #fff;">
        <div class="ant-table-wrapper ant-table-striped">
            <div class="ant-spin-nested-loading">
                <div class="ant-spin-container">
                    <div class="ant-table ant-table-small ant-table-bordered">
                        <!---->
                        <div class="ant-table-container">
                            <div class="ant-table-content">
                                <a-row type="flex" justify="center" align="top">
                                    <h4> Egresos</h4>

                                </a-row>
                                <table style="table-layout: auto;" class="ant-table-striped">
                                    <colgroup></colgroup>
                                    <thead class="ant-table-thead">
                                        <th>Plantel</th>
                                        <th>Cuenta</th>
                                        <th>Concepto</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                    </thead>
                                    <tbody class="ant-table-tbody">
                                        <tr class="ant-table-row ant-table-row-level-0" v-for="egreso in egresos" :key="egreso.id">
                                            <td class="ant-table-cell" colstart="0" colend="0">{{ egreso.plantel }}</td>
                                            <td class="ant-table-cell column-money" colstart="0" colend="0">
                                                {{ egreso.codigo }} {{ egreso.cuenta }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ egreso.concepto }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{ egreso.fecha }} </td>
                                            <td class="ant-table-cell column-money" colstart="2" colend="2">
                                                {{
                                                    Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(egreso.monto)
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">Total Egresos</td>
                                            <td align="right">{{
                                            Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(total_egresos)
                                            }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table>
                                    <tr>
                                        <td colspan="7">Diferencia</td>
                                        <td align="right">{{
                                            Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(total_ingresos-total_egresos)
                                            }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a-col>
</a-row>
</template>

<script>
import Layout from "../../../shared/LayoutPrint";
import {
    ref,
    computed
} from 'vue'

export default {
    layout: Layout,

    components: {},

    props: ['ingresos', 'egresos', 'fecha1', 'fecha2'],

    setup(props) {
        //console.log(props)
        let total_ingresos = computed(() => {
            return props.ingresos.reduce((suma_acumulada, b) => {
                return suma_acumulada + Number(b['monto']);
                //console.log(a);
                //console.log(b);
            }, 0);
        });

        let total_egresos = computed(() => {
            return props.egresos.reduce((suma_acumulada, b) => {
                return suma_acumulada + Number(b['monto']);
                //console.log(a);
                //console.log(b);
            }, 0);
        })

        return {
            total_ingresos,
            total_egresos
        };
    },
};
</script>

<style>
@media print {

    .oculto-impresion,
    .oculto-impresion * {
        display: none !important;
    }

    header {
        position: fixed;
        top: 0;
    }

    th.column-money,
    td.column-money {
        text-align: right !important;
    }
}

th.column-money,
td.column-money {
    text-align: right !important;
}

header {
    position: fixed;
    top: 0;
}
</style>

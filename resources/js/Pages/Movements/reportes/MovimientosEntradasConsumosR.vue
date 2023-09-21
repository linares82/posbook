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
                                        <h3>Movimientos, Entradas y Consumos</h3>

                                    </a-row>

                                    <a-row type="flex" justify="center" align="top">
                                        <h4> Del {{ fecha1 }} Al {{ fecha2 }}</h4>

                                    </a-row>

                                    <table style="table-layout: auto;" class="ant-table-striped">
                                        <colgroup></colgroup>
                                        <thead class="ant-table-thead">

                                            <th>Plantel</th>
                                            <th>Producto</th>
                                            <th>Entrada</th>
                                            <th>Movimiento</th>
                                            <th>Cantidad Entrada</th>
                                            <th>Cantidad Salida</th>
                                            <th>Consumos</th>
                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="movimiento in movimientos"
                                                :key="movimiento.movement_id">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{
                                                    movimiento.plantel
                                                }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    movimiento.producto
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    movimiento.orden
                                                }} </td>
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ movimiento.movement_id }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    movimiento.cantidad_entrada
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                movimiento.cantidad_salida}}
                                                </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                movimiento.consumo}}
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
                                        <h4> </h4>

                                    </a-row>

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

export default {
    layout: Layout,

    components: {},

    props: ['resumen', 'movimientos', 'fecha1', 'fecha2'],

    setup(props) {
        console.log(props)

        return {

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
}

header {
    position: fixed;
    top: 0;
}
</style>

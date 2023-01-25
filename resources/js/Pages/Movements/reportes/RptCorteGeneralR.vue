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
                                        <h3>Corte General</h3>

                                    </a-row>

                                    <a-row type="flex" justify="center" align="top">
                                        <h4> Del {{ fecha1 }} Al {{ fecha2 }}</h4>

                                    </a-row>
                                    <a-row type="flex" justify="center" align="top">
                                        <h4> Resumen</h4>

                                    </a-row>
                                    <table style="table-layout: auto;" class="ant-table-striped">
                                        <colgroup></colgroup>
                                        <thead class="ant-table-thead">
                                            <th>Plantel</th>
                                            <th>Venta Plantel Regular</th>
                                            <th>Devolver por Plantel</th>
                                            <th>Pagar por Plantel</th>
                                            <th>Restante</th>
                                            <th>10%</th>
                                            <th>Ganancia Final Por Plantel</th>

                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="ln in resumen"
                                                :key="ln.control">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ ln.plantel }}</td>
                                                <td class="ant-table-cell" colstart="0" colend="0">{{
                                                    ln.dinero_cantidad_vendida
                                                }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_existencia_por_devolver
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_vendidos_costo
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_cantidad_vendida - ln.dinero_vendidos_costo
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                (ln.dinero_cantidad_vendida - ln.dinero_vendidos_costo) * 0.10 }}
                                                </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_cantidad_vendida - ln.dinero_vendidos_costo -
                                                        (ln.dinero_cantidad_vendida - ln.dinero_vendidos_costo) * 0.10
                                                }} </td>

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
                                        <h4> Detalle</h4>

                                    </a-row>
                                    <table style="table-layout: auto;" class="ant-table-striped">
                                        <colgroup></colgroup>
                                        <thead class="ant-table-thead">
                                            <th>Plantel</th>
                                            <th>Producto</th>
                                            <th>Numero</th>
                                            <th>Libros Vendidos Regular</th>
                                            <th>Libros Restantes</th>
                                            <th>Venta Regular</th>
                                            <th>Por Devolver</th>
                                            <th>Por Pagar</th>
                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="ln in detalle"
                                                :key="ln.movement_id">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ ln.plantel }}
                                                </td>
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ ln.producto }}
                                                </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.cantidad_pedida
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{ ln.vendidos }}
                                                </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.existencia_por_vender
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_cantidad_vendida
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_existencia_por_devolver
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.dinero_vendidos_costo
                                                }} </td>
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
    </a-row>
</template>

<script>
import Layout from "../../../shared/LayoutPrint";

export default {
    layout: Layout,

    components: {},

    props: ['resumen', 'detalle', 'fecha1', 'fecha2'],

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

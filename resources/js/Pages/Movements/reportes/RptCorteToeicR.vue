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
                                        <h3>Corte TOEIC</h3>

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
                                            <th>Pago 1(pagados)</th>
                                            <th>1er Pago(suma)</th>
                                            <th>Pago 2 (Pagados)</th>
                                            <th>2o Pago(suma)</th>
                                            <th>Total</th>
                                            
                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="ln in resumen"
                                                :key="ln.cash_box_id">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ ln.plantel }}</td>
                                                <td class="ant-table-cell" colstart="0" colend="0">{{
                                                    ln.cuenta_pagos1
                                                }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.suma_pagos1
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.cuenta_pagos2
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                    ln.suma_pagos2
                                                }} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2"> {{
                                                ln.suma_pagos1+ln.suma_pagos2}}
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

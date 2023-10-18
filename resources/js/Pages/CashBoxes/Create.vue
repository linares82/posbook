<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Caja" sub-title="Crear" />
    </a-col>
    <a-col :span="12">
        <Link href="/cashBoxes" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Buscar</Link>
        <Link href="/cashBoxes/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Nuevo</Link>
    </a-col>
</a-row>
<a-form ref="formRef" :model="formCashBox" @submit.prevent="submitF" layout="vertical" @finish="onFinish">
    <a-row>

        <a-col :md="7">
            <a-form-item compact label="Plantel" name="plantel_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-select :options="planteles" show-search v-model:value="formCashBox.plantel_id" style="width: 300px" placeholder="Seleccionar Opción" :filter-option="filterOption">
                </a-select>
                <div v-if="errors.plantel_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.plantel_id"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item compact label="Fecha" name="fecha" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formCashBox.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item label="Cliente" name="customer" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-input v-model:value="formCashBox.customer"> </a-input>
                <div v-if="errors.customer">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.customer"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item label="Matricula" name="matricula" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-input v-model:value="formCashBox.matricula"> </a-input>
                <div v-if="errors.matricula">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.matricula"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item label="Total" name="total">
                <a-input v-model:value="formCashBox.total" readonly> </a-input>
                <div v-if="errors.total">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.total"></div>
                </div>
            </a-form-item>
        </a-col>
        <a-col :md="1"></a-col>
        <a-col :md="7">
            <a-form-item label="Entregado" name="bnd_entregado">
                <a-checkbox v-model:checked="formCashBox.bnd_entregado"></a-checkbox>
                <div v-if="errors.total">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_entregado"></div>
                </div>
            </a-form-item>
        </a-col>
        <div>
            <a-modal v-model:visible="visibleLinea" title="Crear Linea" @ok="addLinea">
                <template #footer>
                    <a-button key="back" @click="handleCancelLinea">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loadingLinea" @click="addLinea">Agregar</a-button>
                </template>
                <a-col :md="10">
                    <a-form-item label="Producto" name="product_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                        <a-select @change="handleChange" show-search :options="productos" v-model:value="formCashBox.product_id" style="width: 250px" placeholder="Seleccionar Opción" :filter-option="filterOption">
                        </a-select>
                        <div v-if="errors.product_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.product_id"></div>
                        </div>
                    </a-form-item>
                    </a-col>
                    <a-col :md="8">
                        <a-form-item label="Existencia" name="existencia" >
                            <a-input v-model:value="formCashBox.existencia" readonly> </a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="4">
                        <a-form-item label="Cantidad" name="quantity" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                            <a-input v-model:value="formCashBox.quantity"> </a-input>
                            <div v-if="errors.quantity">
                                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.quantity"></div>
                            </div>
                        </a-form-item>
                    </a-col>
                    <a-col :md="1"></a-col>
                    <a-col :md="4">
                        <a-form-item label="Precio" name="precio" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                            <a-input v-model:value="formCashBox.precio" readonly> </a-input>
                            <div v-if="errors.precio">
                                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.precio"></div>
                            </div>
                        </a-form-item>
                    </a-col>
            </a-modal>
        </div>

        <a-col :md="12" :xs="16" style="border: solid 1px; padding:5px;">
            <a-form-item v-show="formCashBox.lineas.length==0">
                <a-button type="dashed"  block @click="showModalLinea">
                    <PlusOutlined />
                    Agregar Linea
                </a-button>
            </a-form-item>

            <a-col :span="16">
                <a-space v-for="(linea, index) in formCashBox.lineas" :key="linea.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">
                    <a-form-item :name="['linea', index, 'product_id']" label="Producto">
                        <a-select v-model:value="linea.product_id" :options="productos" disabled style="width: 250px" show-search :filter-option="filterOption"></a-select>
                    </a-form-item>

                    <a-form-item :name="['linea', index, 'totalLinea']" label="Total">
                        <a-input v-model:value="linea.totalLinea" style="width: 100px" readonly />
                    </a-form-item>

                    <MinusCircleOutlined @click="removeLinea(linea)" />
                </a-space>
            </a-col>
        </a-col>

        <div>
            <a-modal v-model:visible="visible" title="Agregar Pago" @ok="handleOk">
                <template #footer>
                    <a-button key="back" @click="handleCancel">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loading" @click="handleOk">Agregar</a-button>
                </template>
                <a-col :md="24">
                    <a-form-item label="Metodo de Pago" name="payment_method_id">
                        <a-select :options="paymentMethods" @change="consultaPorcentajeDescuento" show-search v-model:value="formCashBox.payment_method_id" style="width: 250px" placeholder="Seleccionar Opción" :filter-option="filterOption">
                        </a-select>
                        <div v-if="errors.payment_method_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.payment_method_id"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Porcejate Descuento(formato decimal 0.00)" name="porcentaje_descuento">
                        <a-input v-model:value="formCashBox.porcentaje_descuento"> </a-input>
                        <div v-if="errors.porcentaje_descuento">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.porcentaje_descuento"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Monto" name="monto">
                        <a-input v-model:value="formCashBox.monto" v-if="formCashBox.lineas.length>1" > </a-input>
                        <a-input v-model:value="formCashBox.monto" v-else-if="formCashBox.lineas.length==1" readonly> </a-input>
                        <div v-if="errors.monto">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.monto"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Fecha" name="fechaPago">
                        <a-date-picker v-model:value="formCashBox.fechaPago" :bordered="true" />
                        <div v-if="errors.fechaPago">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fechaPago"></div>
                        </div>
                    </a-form-item>
                </a-col>
            </a-modal>
        </div>

        <a-col :md="12" :xs="16" style="border: solid 1px; padding:5px;">
            <a-form-item v-show="formCashBox.payments.reduce((acumulador, payment) => acumulador + payment.monto, 0)<formCashBox.total">
                <a-button type="dashed" block @click="showModal">
                    <PlusOutlined />
                    Agregar Pago
                </a-button>
            </a-form-item>
            <!--<a-row>
                <a-col :md="10">
                    <a-form-item label="Metodo de Pago" name="payment_method_id">
                        <a-select :options="paymentMethods" show-search v-model:value="formCashBox.payment_method_id" style="width: 250px" placeholder="Seleccionar Opción">
                        </a-select>
                        <div v-if="errors.payment_method_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.payment_method_id"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="1"></a-col>
                <a-col :md="4">
                    <a-form-item label="Monto" name="monto">
                        <a-input v-model:value="formCashBox.monto"> </a-input>
                        <div v-if="errors.monto">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.monto"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="1"></a-col>
                <a-col :md="6">
                    <a-form-item label="Fecha" name="fechaPago">
                        <a-date-picker v-model:value="formCashBox.fechaPago" :bordered="true" />
                        <div v-if="errors.fechaPago">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fechaPago"></div>
                        </div>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-form-item>
                <a-button type="dashed" block @click="addPayment">
                    <PlusOutlined />
                    Agregar Pago
                </a-button>
            </a-form-item>
            -->
            <a-col :span="16">
                <a-space v-for="(payment, index) in formCashBox.payments" :key="payment.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">
                    <a-form-item :name="['payment', index, 'payment_method_id']" label="Metodo Pago">
                        <a-select v-model:value="payment.payment_method_id" :options="paymentMethods" disabled style="width: 250px" show-search :filter-option="filterOption"></a-select>
                    </a-form-item>

                    <a-form-item :name="['payment', index, 'monto']" label="Monto">
                        <a-input v-model:value="payment.monto" style="width: 100px" readonly />
                    </a-form-item>

                    <a-form-item :name="['fecha', index, 'fecha']" label="Fecha">
                        <a-date-picker v-model:value="payment.fecha" style="width: 150px" disabled :bordered="true" />
                    </a-form-item>

                    <MinusCircleOutlined @click="removePayment(payment)" />
                </a-space>
            </a-col>
        </a-col>

        <a-col :span="1"></a-col>
    </a-row>
    <a-row></a-row>

    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processing">Crear</a-button>
    </a-form-item>
</a-form>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    defineComponent,
    reactive,
    computed,
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
    PlusOutlined,
    UserOutlined,
    LockOutlined
} from "@ant-design/icons-vue";
import {
    Inertia
} from "@inertiajs/inertia";
import axios from 'axios';
//import locale from 'ant-design-vue/es/date-picker/locale/es_Es';
/*
import dayjs from 'dayjs';
import 'dayjs/locale/es-mx';
dayjs.locale('es-mx');
*/
export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
        MinusCircleOutlined,
        PlusOutlined,
    },

    props: ["errors", 'productos', 'planteles', 'estatus', 'productos', 'plantel', 'paymentMethods',
    'ruta_productos_findById', 'ruta_consulta_porcentaje_descuento'],

    setup(props) {
        const formRef = ref();

        let formCashBox = reactive({
            plantel_id: props.plantel,
            fecha: undefined,
            customer: undefined,
            matricula:undefined,
            st_cash_box_id: undefined,
            total: 0,
            bnd_entregado: false,
            product_id: undefined,
            existencia: 0,
            precio: 0,
            quantity: 1,
            payment_method_id: undefined,
            porcentaje_descuento:0,
            monto: 0,
            fechaPago: undefined,
            lineas: [],
            payments: []
        });

        let consultaProductos;

        let processing = ref(false);

        const removeLinea = item => {
            let index = formCashBox.lineas.indexOf(item);

            if (index !== -1) {
                formCashBox.lineas.splice(index, 1);
            }

            let total = 0;
            for (let linea in formCashBox.lineas) {
                total = total + parseFloat(formCashBox.lineas[linea].totalLinea);
            }
            formCashBox.total = total;
            formCashBox.monto = total;
        };

        const removePayment = item => {
            let index = formCashBox.payments.indexOf(item);

            if (index !== -1) {
                formCashBox.payments.splice(index, 1);
            }
        };

        const addLinea = () => {
            //console.log(consultaProductos);
            loadingLinea.value = true;
            formCashBox.lineas.push({
                product_id: formCashBox.product_id,
                quantity: formCashBox.quantity,
                precio: formCashBox.precio,
                totalLinea: formCashBox.quantity * formCashBox.precio
            });
            if (consultaProductos.product_id > 0 && consultaProductos.product_id !== null) {
                axios.get(props.ruta_productos_findById + "?producto=" + consultaProductos.product_id + "&plantel="+formCashBox.plantel_id)
                    .then(response => {
                        //formCashBox.precio = response.data.precio;
                        //consultaProductos=response.data;
                        //console.log(response.data);
                        formCashBox.lineas.push({
                            product_id: response.data.producto.id,
                            quantity: 1,
                            precio: 0,
                            totalLinea: 0,
                            existencia:response.data
                        });
                    });
            }
            if (consultaProductos.exam_id > 0 && consultaProductos.exam_id !== null) {
                axios.get(props.ruta_productos_findById + "?producto=" + consultaProductos.exam_id + "&plantel="+formCashBox.plantel_id)
                    .then(response => {
                        //formCashBox.precio = response.data.precio;
                        //consultaProductos=response.data;
                        //console.log(response.data);
                        formCashBox.lineas.push({
                            product_id: response.data.producto.id,
                            quantity: 1,
                            precio: 0,
                            totalLinea: 0,
                            existencia:response.data
                        });
                    });
            }
            let total = 0;
            for (let linea in formCashBox.lineas) {
                total = total + parseFloat(formCashBox.lineas[linea].totalLinea);
            }
            formCashBox.total = total;
            formCashBox.monto = total;

            formCashBox.product_id = null;
            formCashBox.precio = 0;

            loadingLinea.value = false;
            visibleLinea.value = false;
            //console.log(formCashBox);
        };

        const addPayment = () => {

            if(formCashBox.porcentaje_descuento>0){
                formCashBox.monto=formCashBox.total*formCashBox.porcentaje_descuento;
            }

            formCashBox.payments.push({
                payment_method_id: formCashBox.payment_method_id,
                monto: formCashBox.total,
                procentaje_descuento: formCashBox.porcentaje_descuento,
                fecha: formCashBox.fechaPago
            });

            //console.log(formCashBox);
        };

        const onFinish = values => {
            //console.log('Received values of form:', values);
            //console.log('formCashBox:', formCashBox);
        };

        let submitF = () => {
            Inertia.post("/cashBoxes/store", formCashBox, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };

        /*
        const handleChange = value => {
            console.log(value.data); // { key: "lucy", label: "Lucy (101)" }
        };*/

        const handleChange = value => {
            //console.log("{{$ruta_productos_findById}}");
            axios.get(props.ruta_productos_findById + "?producto=" + value + "&plantel="+formCashBox.plantel_id)
                .then(response => {
                    //console.log(response.data);
                    formCashBox.existencia=response.data.existencia;
                    formCashBox.precio = response.data.producto.precio;
                    consultaProductos = response.data.producto;
                });

        };

        //ventana modal
        const loading = ref(false);
        const visible = ref(false);
        const loadingLinea = ref(false);
        const visibleLinea = ref(false);

        const showModal = () => {
            formCashBox.payment_method_id=0;
            formCashBox.porcentaje_descuento=0;
            let suma_pagos = formCashBox.payments.reduce((acumulador, payment) => acumulador + payment.monto, 0);
            formCashBox.monto=formCashBox.total-suma_pagos;

            visible.value = true;
        };

        const showModalLinea = () => {
            visibleLinea.value = true;
        };

        const handleOk = () => {
            loading.value = true;
            if(formCashBox.porcentaje_descuento>0){
                formCashBox.monto=formCashBox.total*formCashBox.porcentaje_descuento;
            }

            formCashBox.payments.push({
                payment_method_id: formCashBox.payment_method_id,
                monto: formCashBox.monto,
                porcentaje_descuento: formCashBox.porcentaje_descuento,
                fecha: formCashBox.fechaPago
            });
            //console.log(formCashBox);
            loading.value = false;
            visible.value = false;
        };

        const handleCancel = () => {
            visible.value = false;
        };

        const handleCancelLinea = () => {
            visibleLinea.value = false;
        };

        /*
        const porc_desc_calculado = computed(() => {
            if(formCashBox.payment_method_id==4 && formCashBox.porcentaje_descuento>0){
                formCashBox.monto=formCashBox.total*formCashBox.porcentaje_descuento;
            }

        });*/
        const consultaPorcentajeDescuento = value => {
            //console.log(props.ruta_consulta_porcentaje_descuento);
            axios.get(props.ruta_consulta_porcentaje_descuento +
                        "?id=" + value )
                    .then(response => {
                        //console.log(response);
                    formCashBox.porcentaje_descuento=response.data.porcentaje_descuento;
                });

        };

        const filterOption = (input, option) => {
            //console.log(option);
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        return {
            //porc_desc_calculado,
            consultaPorcentajeDescuento,
            consultaProductos,
            handleChange,
            formCashBox,
            submitF,
            processing,
            //dayjs,
            //locale
            formRef,
            onFinish,
            removeLinea,
            addLinea,
            removePayment,
            addPayment,
            loading,
            visible,
            showModal,
            handleOk,
            handleCancel,
            loadingLinea,
            visibleLinea,
            showModalLinea,
            handleCancelLinea,
            filterOption
        };
    },
};
</script>

<style>
</style>

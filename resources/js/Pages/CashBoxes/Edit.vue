<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Caja" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
        <Link href="/cashBoxes" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button"><i>
            <search-outlined /></i>Buscar</Link>
        <Link href="/cashBoxes/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button"><i>
            <book-outlined /></i>Nuevo</Link>
        <a :href="route_ticket" target="_blank" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button"><i>
                <printer-outlined /></i>Ticket</a>

    </a-col>
</a-row>
<a-form :model="formCashBox" @submit.prevent="submitF" autocomplete="off" layout="vertical">
    <a-row :gutter="20">
        <a-col :md="2">
            <a-form-item label="No. Unico">
                {{formCashBox.id}}
            </a-form-item>
        </a-col>

        <a-col :md="2">
            <a-form-item label="Estatus">
                {{formCashBox.st_cash_box}}
            </a-form-item>
        </a-col>

        <a-col :md="4">
            <a-form-item compact label="Plantel" name="plantel_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-select :options="planteles" show-search v-model:value="formCashBox.plantel_id" style="width: 100%" placeholder="Seleccionar Opción">

                </a-select>
                <div v-if="errors.plantel_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.plantel_id"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="6">
            <a-form-item label="Cliente" name="customer" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-input v-model:value="formCashBox.customer"> </a-input>
                <div v-if="errors.customer">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.customer"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :sm="4" :md="3">
            <a-form-item label="Matricula" name="matricula" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-input v-model:value="formCashBox.matricula"> </a-input>
                <div v-if="errors.matricula">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.matricula"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :sm="4" :md="3">
            <a-form-item compact label="Fecha" name="fecha" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-date-picker v-model:value="formCashBox.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :sm="4" :md="3">
            <a-form-item label="Total" name="total">
                <a-input v-model:value="formCashBox.total" readonly> </a-input>
                <div v-if="errors.total">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.total"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="3">
            <a-form-item label="Entregado" name="bnd_entregado">
                <a-checkbox v-model:checked="formCashBox.bnd_entregado"></a-checkbox>
                <div v-if="errors.total">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_entregado"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="3" v-show="formCashBox.movement_id">
            <a-alert message="Apartado, sin stock" type="info" show-icon />
        </a-col>

        <a-col :md="3">
            <a-form-item label="Referencia" name="reference">
                <a-input v-model:value="formCashBox.reference"> </a-input>
                <div v-if="errors.reference">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.reference"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="3">
            <a-form-item label="Referencia Revisada" name="bnd_referencia_revisada">
                <a-checkbox v-model:checked="formCashBox.bnd_referencia_revisada"></a-checkbox>
                <div v-if="errors.bnd_referencia_revisada">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.bnd_referencia_revisada"></div>
                </div>
            </a-form-item>
        </a-col>

        <div>
            <a-modal v-model:visible="visibleLinea" title="Agregar Linea" @ok="addLinea">
                <template #footer>
                    <a-button key="back" @click="handleCancelLinea">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loadingLinea" @click="addLinea">Agregar</a-button>
                </template>
                <a-row :gutter="20">
                    <a-col :md="12">
                        <a-form-item label="Producto" name="product_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                            <a-select @change="handleChangeProducto" show-search :options="productos" v-model:value="formCashBox.product_id" style="width: 100%;" placeholder="Seleccionar Opción">
                            </a-select>
                            <div v-if="errors.product_id">
                                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.product_id"></div>
                            </div>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12">
                        <a-form-item label="Existencia" name="existencia">
                            <a-input v-model:value="formCashBox.existencia" readonly> </a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12">
                        <a-form-item label="Cantidad" name="quantity" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                            <a-input v-model:value="formCashBox.quantity"> </a-input>
                            <div v-if="errors.quantity">
                                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.quantity"></div>
                            </div>
                        </a-form-item>
                    </a-col>

                    <a-col :md="12">
                        <a-form-item label="Precio" name="precio" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                            <a-input v-model:value="formCashBox.precio" readonly> </a-input>
                            <div v-if="errors.precio">
                                <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.precio"></div>
                            </div>
                        </a-form-item>
                    </a-col>
                </a-row>

            </a-modal>
        </div>

        <div>
            <a-modal v-model:visible="visibleEditLinea" title="Editar Linea" @ok="handleOkEditLinea">
                <template #footer>
                    <a-button key="back" @click="handleCancelEditLinea">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loadingEditLinea" @click="handleOkEditLinea">Actualizar</a-button>
                </template>
                <a-col :md="10">
                    <a-form-item label="Producto" name="product_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                        <a-select @change="handleChangeProducto" show-search :options="productos" v-model:value="formCashBox.product_id" style="width: 250px" placeholder="Seleccionar Opción">
                        </a-select>
                        <div v-if="errors.product_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.product_id"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="1"></a-col>
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

        <a-col :md="8"></a-col>

        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" style="border: solid 1px; padding:5px;">
            <a-form-item v-show="formCashBox.lineas.length==0 || permissions.cashBoxesManyLines ">
                <a-button type="dashed" block @click="showModalLinea">
                    <PlusOutlined />
                    Agregar Linea
                </a-button>
            </a-form-item>
            <a-col>
                <table style="table-layout: auto;" class="ant-table-striped">
                    <thead class="ant-table-thead">
                        <th class="ant-table-cell">Id</th>
                        <th class="ant-table-cell">Producto</th>
                        <th class="ant-table-cell">Unidades</th>
                        <th class="ant-table-cell">Total</th>
                        <th class="ant-table-cell"></th>
                    </thead>
                    <tbody class="ant-table-tbody">
                        <!--<a-space v-for="(linea, index) in formCashBox.lineas" :key="linea.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">-->
                        <tr class="ant-table-row ant-table-row-level-0" v-for="(linea, index) in formCashBox.lineas" :key="linea.tiempo_id">
                            <td>
                                {{linea.id}}
                                <!--
                                <a-form-item :name="['id', index, 'id']" label="">
                                    <a-input v-model:value="linea.id" style="width: 100px" readonly />
                                </a-form-item>
                                -->
                            </td>
                            <td>
                                {{linea.product}}
                                <!--
                                <a-form-item :name="['linea', index, 'product_id']" label="">
                                    <a-select v-model:value="linea.product_id" :options="productos" disabled style="width: 250px" show-search></a-select>
                                </a-form-item>
                                -->
                            </td>
                            <td>{{ linea.quantity }}</td>
                            <td>
                                {{ linea.totalLinea}}
                                <!--<a-form-item :name="['linea', index, 'totalLinea']" label="">
                                    <a-input v-model:value="linea.totalLinea" style="width: 100px" readonly />
                                </a-form-item>
                                -->
                            </td>
                            <td>
                                <a-popconfirm title="Estas seguro de la operación?" ok-text="Si" cancel-text="No" @confirm="removeLinea(linea)">
                                    <MinusCircleOutlined />
                                </a-popconfirm>
                                <!--<EditOutlined @click="editarLinea(linea)" />-->
                            </td>
                        </tr>
                        <!--</a-space>-->
                    </tbody>
                </table>

            </a-col>
        </a-col>

        <div>
            <a-modal v-model:visible="visiblePayment" title="Agregar Pago" @ok="addPayment">
                <template #footer>
                    <a-button key="back" @click="handleCancelPayment">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loadingPayment" @click="addPayment">Agregar</a-button>
                </template>
                <a-col :md="24">
                    <a-form-item label="Metodo de Pago" name="payment_method_id">
                        <a-select :options="paymentMethods" show-search @change="consultaPorcentajeDescuento" v-model:value="formCashBox.payment_method_id" style="width: 250px" placeholder="Seleccionar Opción">
                        </a-select>
                        <div v-if="errors.payment_method_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.payment_method_id"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Porcejate Descuento(formato decimal 0.00)" name="porcentaje_descuento">
                        <a-input v-model:value="formCashBox.porcentaje_descuento" readonly> </a-input>
                        <div v-if="errors.porcentaje_descuento">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.porcentaje_descuento"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Monto" name="monto">
                        <a-input v-model:value="formCashBox.monto" v-if="formCashBox.lineas.length>1"> </a-input>
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

        <div>
            <a-modal v-model:visible="visibleEditPayment" title="Editar Pago" @ok="handleOkEditPayment">
                <template #footer>
                    <a-button key="back" @click="handleCancelPayment">Cancelar</a-button>
                    <a-button key="submit" type="primary" :loading="loadingEditPayment" @click="handleOkEditPayment">Actualizar</a-button>
                </template>
                <a-col :md="24">
                    <a-form-item label="Metodo de Pago" name="payment_method_id">
                        <a-select :options="paymentMethods" show-search v-model:value="formCashBox.payment_method_id" style="width: 250px" placeholder="Seleccionar Opción">
                        </a-select>
                        <div v-if="errors.payment_method_id">
                            <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.payment_method_id"></div>
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :md="24">
                    <a-form-item label="Monto" name="monto">
                        <a-input v-model:value="formCashBox.monto"> </a-input>
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

        <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" style="border: solid 1px; padding:5px;"
            v-show="permissions.cashBoxesPayments"
        >
            <a-form-item v-show="formCashBox.payments.reduce((acumulador, payment) => acumulador + payment.monto, 0)<formCashBox.total">
                <a-button type="dashed" block @click="showModalPayment">
                    <PlusOutlined />
                    Agregar Pago
                </a-button>
            </a-form-item>

            <a-col :span="16">
                <table>
                    <thead class="ant-table-thead">
                        <th class="ant-table-cell">Id</th>
                        <th class="ant-table-cell">Metodo Pago</th>
                        <th class="ant-table-cell">% Desc.</th>
                        <th class="ant-table-cell">Monto</th>
                        <th class="ant-table-cell">Fecha</th>
                        <th class="ant-table-cell"></th>
                    </thead>
                    <tbody class="ant-table-tbody">
                        <tr class="ant-table-row ant-table-row-level-0" v-for="(payment, index) in formCashBox.payments" :key="payment.tiempo_id">
                            <td>
                                {{payment.id}}
                                <!--<a-form-item :name="['payment', index, 'id']" label="">
                                    <a-input v-model:value="payment.id" style="width: 100px" readonly />
                                </a-form-item>
                                -->
                            </td>
                            <td>
                                {{payment.payment_method}}
                                <!--
                                <a-form-item :name="['payment', index, 'payment_method_id']" label="">
                                    <a-select v-model:value="payment.payment_method_id" :options="paymentMethods" disabled style="width: 250px" show-search></a-select>
                                </a-form-item>-->
                            </td>
                            <td>
                                {{payment.porcentaje_descuento}}
                            </td>
                            <td>
                                {{payment.monto}}
                                <!--
                                <a-form-item :name="['payment', index, 'monto']" label="">
                                    <a-input v-model:value="payment.monto" style="width: 100px" readonly />
                                </a-form-item>-->
                            </td>
                            <td>
                                {{payment.fecha.format('YYYY-MM-DD')}}
                                <!--
                                <a-form-item :name="['fecha', index, 'fecha']" label="">
                                    <a-date-picker v-model:value="payment.fecha" style="width: 150px" disabled :bordered="true" />
                                </a-form-item>
                                -->
                            </td>
                            <td>
                                <a-popconfirm title="Estas seguro de la operación?" ok-text="Si" cancel-text="No" @confirm="removePayment(payment)">
                                    <MinusCircleOutlined />
                                </a-popconfirm>
                                <!--<EditOutlined @click="editarPayment(payment)" />-->
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--<a-space v-for="(payment, index) in formCashBox.payments" :key="payment.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">    </a-space>-->
            </a-col>
        </a-col>

        <a-col :span="1"></a-col>
    </a-row>
    <a-row></a-row>

    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processingPayment">Actualizar</a-button>
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
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
    PlusOutlined,
    UserOutlined,
    LockOutlined,
    EditOutlined,
    PrinterOutlined,
    SearchOutlined,
    BookOutlined
} from "@ant-design/icons-vue";
import {
    Inertia
} from "@inertiajs/inertia";
import axios from 'axios';
import dayjs from 'dayjs';
import debounce from "lodash/debounce";

export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
        MinusCircleOutlined,
        PlusOutlined,
        EditOutlined,
        PrinterOutlined,
        SearchOutlined,
        BookOutlined
    },

    props: ['ruta_destroy_ln', 'ruta_update_ln', 'cashBox', "errors", 'productos', 'planteles',
        'estatus', 'productos', 'plantel', 'paymentMethods', 'ruta_productos_findById',
        'ruta_update_payment', 'ruta_destroy_payment', 'ruta_update_cashBox',
        'ruta_consulta_porcentaje_descuento','permissions'
    ],

    setup(props) {
        const formRef = ref();
        const route_ticket = "/cashBoxes/ticket/" + props.cashBox.id

        let formCashBox = reactive({
            id: props.cashBox.id,
            plantel_id: props.cashBox.plantel_id,
            plantel: props.cashBox.plantel,
            fecha: dayjs(props.cashBox.fecha, 'YYYY/MM/DD'),
            customer: props.cashBox.customer,
            matricula: props.cashBox.matricula,
            st_cash_box_id: props.cashBox.st_cash_box_id,
            st_cash_box: props.cashBox.st_cash_box,
            total: props.cashBox.total,
            bnd_entregado: props.cashBox.bnd_entregado,
            reference: props.cashBox.reference,
            bnd_referencia_revisada: props.cashBox.bnd_referencia_revisada,
            linea_id: undefined,
            product_id: undefined,
            existencia: undefined,
            precio: 0,
            quantity: 1,
            payment_id: undefined,
            payment_method_id: undefined,
            porcentaje_descuento: 0,
            monto: 0,
            fechaPago: undefined,
            movement_id: false,
            lineas: [],
            payments: []
        });

        //console.log(props.cashBox.lineas);
        for (let linea in props.cashBox.lineas) {
            formCashBox.lineas.push(props.cashBox.lineas[linea])
            //console.log(props.cashBox.lineas[linea].movement_id===null);
            if (props.cashBox.lineas[linea].movement_id === null && props.cashBox.lineas[linea].precio > 0) {
                formCashBox.movement_id = true;
            } else if (props.cashBox.lineas[linea].movement_id === null && props.cashBox.lineas[linea].precio == 0) {
                formCashBox.movement_id = true;
            } else {
                formCashBox.movement_id = false;
            }
        }
        //console.log(formCashBox.movement_id);

        for (let payment in props.cashBox.payments) {
            formCashBox.payments.push({
                id: props.cashBox.payments[payment].id,
                cash_box_id: props.cashBox.payments[payment].cash_box_id,
                fecha: dayjs(props.cashBox.payments[payment].fecha, 'YYYY/MM/DD'),
                id: props.cashBox.payments[payment].id,
                monto: props.cashBox.payments[payment].monto,
                porcentaje_descuento: props.cashBox.payments[payment].porcentaje_descuento,
                payment_method: props.cashBox.payments[payment].payment_method,
                payment_method_id: props.cashBox.payments[payment].payment_method_id,
                st_payment: props.cashBox.payments[payment].st_payment,
                st_payment_id: props.cashBox.payments[payment].st_payment_id

            });
            //formCashBox.payments.push(props.cashBox.payments[payment])
        }

        let consultaProductos;
        let processingLinea = ref(false);
        const loadingLinea = ref(false);
        const visibleLinea = ref(false);
        let le = undefined;

        let processingEditLinea = ref(false);
        const loadingEditLinea = ref(false);
        const visibleEditLinea = ref(false);

        //Guardado de Fomrulario completo
        let submitF = () => {
            Inertia.post(props.ruta_update_cashBox, formCashBox, {
                onStart: () => {
                    processingLinea.value = true;
                },
                onFinish: () => {
                    processingLinea.value = false;
                    location.reload();
                },
            });
        };

        //trabajo con Lineas
        const showModalLinea = () => {
            visibleLinea.value = true;
        };

        const addLinea = () => {
            //console.log(consultaProductos);
            loadingLinea.value = true;
            formCashBox.lineas.push({
                product_id: formCashBox.product_id,
                product: consultaProductos.name,
                quantity: formCashBox.quantity,
                precio: formCashBox.precio,
                totalLinea: formCashBox.quantity * formCashBox.precio
            });
            //console.log(formCashBox);
            if (consultaProductos.product_id > 0 && consultaProductos.product_id !== null) {
                axios.get(props.ruta_productos_findById +
                        "?producto=" + consultaProductos.product_id +
                        "&plantel=" + formCashBox.plantel_id)
                    .then(response => {
                        //formCashBox.precio = response.data.precio;
                        //consultaProductos=response.data;
                        //console.log(response.data);
                        formCashBox.lineas.push({
                            product_id: response.data.id,
                            quantity: 1,
                            precio: 0,
                            totalLinea: 0
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
        };

        const showModalEditLinea = () => {
            visibleLinea.value = true;
        };

        const removeLinea = item => {
            if (item.hasOwnProperty('id')) {

                Inertia.delete(props.ruta_destroy_ln + "?id=" + item.id, null, {
                    onStart: () => {
                        processingLinea.value = true;
                    },
                    onFinish: () => {
                        processingLinea.value = false;
                        location.reload();
                    },
                })

            }
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

        const editarLinea = (linea) => {
            formCashBox.linea_id = linea.id;
            formCashBox.product_id = linea.product_id;
            formCashBox.precio = linea.precio;
            formCashBox.quantity = linea.quantity;
            visibleEditLinea.value = true;
        };

        const handleOkEditLinea = () => {
            loadingEditLinea.value = true;

            Inertia.post(props.ruta_update_ln, formCashBox, {
                onStart: () => {
                    processingLinea.value = true;
                },
                onFinish: () => {
                    processingLinea.value = false;
                    location.reload();
                },
            });
            loadingEditLinea.value = false;
            visibleEditLinea.value = false;

        };

        const handleChangeProducto = value => {
            //console.log("{{$ruta_productos_findById}}");
            axios.get(props.ruta_productos_findById +
                    "?producto=" + value +
                    "&plantel=" + formCashBox.plantel_id)
                .then(response => {
                    formCashBox.existencia = response.data.existencia;
                    formCashBox.precio = response.data.producto.precio;
                    consultaProductos = response.data.producto;
                });

        };

        const handleCancelLinea = () => {
            visibleLinea.value = false;
        };

        const handleCancelEditLinea = () => {
            visibleEditLinea.value = false;
        };

        //Fin trabajo con lineas

        //Trabajo con pagos
        let processingPayment = ref(false);
        const loadingPayment = ref(false);
        const visiblePayment = ref(false);

        let processingEditPayment = ref(false);
        const loadingEditPayment = ref(false);
        const visibleEditPayment = ref(false);

        const showModalPayment = () => {
            formCashBox.payment_method_id = 0;
            formCashBox.porcentaje_descuento = 0;
            let suma_pagos = formCashBox.payments.reduce((acumulador, payment) => acumulador + payment.monto, 0);
            formCashBox.monto = formCashBox.total - suma_pagos;

            visiblePayment.value = true;
        };

        const showModalEditPayment = () => {
            visiblePayment.value = true;
        };

        const removePayment = item => {
            if (item.hasOwnProperty('id')) {

                Inertia.delete(props.ruta_destroy_payment + "?id=" + item.id, null, {
                    onStart: () => {
                        processingLinea.value = true;
                    },
                    onFinish: () => {
                        processingLinea.value = false;
                        location.reload();
                    },
                })

            }

            let index = formCashBox.payments.indexOf(item);

            if (index !== -1) {
                formCashBox.payments.splice(index, 1);
            }
        };

        const editarPayment = (payment) => {
            //console.log(payment.fecha.toDate())

            formCashBox.payment_id = payment.id;
            formCashBox.payment_method_id = payment.payment_method_id;
            formCashBox.monto = payment.monto;
            formCashBox.fechaPago = payment.fecha;
            visibleEditPayment.value = true;
        };

        const handleOkEditPayment = () => {
            loadingEditPayment.value = true;
            Inertia.post(props.ruta_update_payment, formCashBox, {
                onStart: () => {
                    processingEditPayment.value = true;
                },
                onFinish: () => {
                    processingEditPayment.value = false;
                    location.reload();
                },
            });
            loadingEditPayment.value = false;
            visibleEditPayment.value = false;
        };

        const addPayment = () => {
            loadingPayment.value = true;

            if (formCashBox.porcentaje_descuento > 0) {
                formCashBox.monto = formCashBox.total * formCashBox.porcentaje_descuento;
            }

            formCashBox.payments.push({
                payment_method_id: formCashBox.payment_method_id,
                porcentaje_descuento: formCashBox.porcentaje_descuento,
                monto: formCashBox.monto,
                fecha: formCashBox.fechaPago
            });
            loadingPayment.value = false;
            visiblePayment.value = false;
        };

        const handleCancelPayment = () => {
            visiblePayment.value = false;
        };

        const handleCancelEditPayment = () => {
            visibleEdit.value = false;
        };

        //Fin trabajo con pagos

        const consultaPorcentajeDescuento = value => {
            //console.log(props.ruta_consulta_porcentaje_descuento);
            axios.get(props.ruta_consulta_porcentaje_descuento +
                    "?id=" + value)
                .then(response => {
                    //console.log(response);
                    formCashBox.porcentaje_descuento = response.data.porcentaje_descuento;
                });

        };

        return {
            consultaPorcentajeDescuento,
            addLinea,
            addPayment,
            consultaProductos,
            editarLinea,
            editarPayment,
            formCashBox,
            formRef,
            //handleOkLinea,
            handleOkEditLinea,
            handleOkEditPayment,
            //handleCancel,
            handleCancelLinea,
            handleCancelEditLinea,
            handleCancelPayment,
            handleCancelEditPayment,
            handleChangeProducto,
            loadingLinea,
            loadingEditLinea,
            loadingPayment,
            loadingEditPayment,
            //onFinish,
            //processing,
            processingLinea,
            processingPayment,
            removeLinea,
            //removeEditLinea,
            removePayment,
            //removeEditPayment,
            submitF,
            //showModal,
            //showModalEdit,
            showModalLinea,
            showModalEditLinea,
            showModalPayment,
            showModalEditPayment,
            visibleLinea,
            visibleEditLinea,
            visiblePayment,
            visibleEditPayment,
            route_ticket
        };
    },
};
</script>

<style>
</style>

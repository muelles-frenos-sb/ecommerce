<?php
$factura = $this->productos_model->obtener('factura', ['id' => $datos['id']]);
print_r($factura);
?>
<script>
    var checkout = new WidgetCheckout({
        currency: 'COP',
        amountInCents: parseFloat(<?php echo $factura->valor; ?>) * 100,
        reference: '<?php echo $factura->token; ?>',
        publicKey: 'pub_test_05yNa2NGkuB1CJhYLt6lflBHe0xTu3I2',
        redirectUrl: `${$('#site_url').val()}carrito/respuesta?referencia=<?php echo $factura->token; ?>`, // Opcional
        // expirationTime: '2023-06-09T20:28:50.000Z', // Opcional
        // taxInCents: { // Opcional
        //     vat: 1900,
        //     consumption: 800
        // }
        // customerData: { // Opcional
        //     email: factura.email,
        //     fullName: `${factura.nombres} ${factura.primer_apellido} ${factura.segundo_apellido}`,
        //     phoneNumber: factura.telefono,
        //     phoneNumberPrefix: '+57',
        //     // legalId: '123456789',
        //     // legalIdType: 'CC'
        // },
        // shippingAddress: { // Opcional
        //     addressLine1: factura.direccion,
        //     city: "Medellín",
        //     phoneNumber: factura.telefono,
        //     region: "Antioquia",
        //     country: "CO"
        // }
    })

    checkout.open(function (resultado) {
        console.log(resultado)
        var transaction = resultado.transaction
        console.log('Transaction ID: ', transaction.id)
        console.log('Transaction object: ', transaction)
    })
</script>
<?php $factura = $this->productos_model->obtener('factura', ['id' => $datos['id']]); ?>

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
        customerData: { // Opcional
            email: '<?php echo $factura->email; ?>',
            fullName: `<?php echo $factura->razon_social; ?>`,
            phoneNumber: `<?php echo $factura->telefono; ?>`,
            phoneNumberPrefix: '+57',
        //     // legalId: '123456789',
        //     // legalIdType: 'CC'
        },
        // shippingAddress: { // Opcional
            // addressLine1: '',
        //     city: "Medellín",
        //     phoneNumber: factura.telefono,
        //     region: "Antioquia",
        //     country: "CO"
        // }
    })

    checkout.open(function (resultado) {
        var transaction = resultado.transaction

        // Se redirecciona a la página de resultados
        location.href = transaction.redirectUrl
        
        // Si se aprobó la transacción, se vacea el carrito
        if(transaction.status == 'APPROVED') vaciarCarrito()

        // console.log('Transaction ID: ', transaction.id)
        // console.log('Transaction object: ', transaction)
    })
</script>
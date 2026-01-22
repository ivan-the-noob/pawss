     function submitDeclineForm() {
        const form = document.getElementById('appointmentForm');
        form.action = '../../function/php/decline_order.php';
        form.submit();
    }

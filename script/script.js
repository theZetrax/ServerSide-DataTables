function format (d) {
    const details = $('<p></p>', { class: "lead" });
    const editBtn = $('<button></button>', { class: "btn btn-warning text-white mr-1" });
    editBtn.html("Edit");
    const deleteBtn = $('<button></button>', { class: "btn btn-danger text-white" });
    deleteBtn.html("Delete");

    editBtn.on('click', function() {
        console.log(`Editing customer ${d.id}`);
        console.log(d);
    });
    
    details.html(`${d.first_name} ${d.last_name} has email ${d.email}`);
    details.append($('<br />'));
    details.append(editBtn);
    details.append(deleteBtn);
    
    return details;
}

$(function () {
    dt = $("#example").DataTable({
        processing: true,
        serverSide: true,
        ajax: "php/api.php",
        columns: [{
                class: "details-control",
                orderable: false,
                data: null,
                defaultContent: ""
            },
            {
                data: "first_name",
            },
            {
                data: "last_name",
            },
            {
                data: "email",

            },
        ],
        "scrollY": "400px"
    });

    var detailRows = [];

    $('#example tbody').on('click', 'tr td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = dt.row( tr );
        let idx = $.inArray( tr.attr('id'), detailRows );

        if ( row.child.isShown() )
        {
            // If row details is shown,
            // Remove shown class
            tr.removeClass( 'details' );
            // Hide row child
            row.child.hide();

            detailRows.splice(idx, 1);
        } else {
            tr.addClass( 'details' );
            row.child( format(row.data())).show();

            if (idx === -1) {
                detailRows.push(tr.attr('id'));
            }
        }
    });

    dt.on('draw', function() {
        $.each(detailRows, function (i, id) {
            $(`#${id} td.details-control`).trigger('click');
        });
    });
    
});
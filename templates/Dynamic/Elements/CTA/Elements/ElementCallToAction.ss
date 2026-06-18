<div class="alert alert-primary" role="alert">
    <div class="px-2 py-2 text-center element__calltoaction">
        <% if $ShowTitle %><h2 class="fw-bold content-element__title">$Title</h2><% end_if %>
        <% if $HTML || $CtaLink %>
            <div class="col-lg-10 mx-auto">
                <% if $HTML %>
                    <div class="lead mb-4 content-element__html">$HTML</div>
                <% end_if %>
                <% if $CtaLink %>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center content-element_ctalink">
                        <% with $CtaLink %>
                            <a href="$URL" <% if $OpenInNew %>target="_blank" rel="noopener noreferrer"<% end_if %> class="btn btn-primary">$Title</a>
                        <% end_with %>
                    </div>
                <% end_if %>
            </div>
        <% end_if %>
    </div>
</div>

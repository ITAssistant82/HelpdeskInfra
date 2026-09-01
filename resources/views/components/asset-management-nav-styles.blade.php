<style>
    .asset-management-nav-parent > .fi-sidebar-item-btn {
        cursor: pointer;
    }

    .asset-management-nav-parent > .fi-sidebar-item-btn::after {
        width: .45rem;
        height: .45rem;
        margin-left: auto;
        border-right: 1.5px solid currentColor;
        border-bottom: 1.5px solid currentColor;
        content: '';
        opacity: .65;
        transform: rotate(45deg);
        transition: transform 200ms ease;
    }

    .asset-management-nav-parent[data-children-open="false"] > .fi-sidebar-item-btn::after {
        transform: rotate(-45deg);
    }

    .asset-management-nav-parent > .fi-sidebar-sub-group-items {
        overflow: hidden;
    }

    .asset-management-nav-parent[data-children-open="false"] > .fi-sidebar-sub-group-items {
        display: none;
    }
</style>

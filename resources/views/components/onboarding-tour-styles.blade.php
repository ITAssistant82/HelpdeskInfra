<style>
    .infra-tour-trigger { position: fixed; right: 1.5rem; bottom: 1.5rem; z-index: 9990; display: inline-flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; padding: 0; border: 0; border-radius: 9999px; color: #fff; background: #d97706; box-shadow: 0 10px 25px rgba(180, 83, 9, .36); cursor: pointer; transition: transform .18s ease, background .18s ease, box-shadow .18s ease; }
    .infra-tour-trigger:hover { background: #b45309; box-shadow: 0 14px 30px rgba(180, 83, 9, .45); transform: translateY(-2px) scale(1.04); }
    .infra-tour-trigger:focus-visible { outline: 3px solid #fcd34d; outline-offset: 3px; }
    .infra-tour-trigger svg { width: 1.6rem; height: 1.6rem; }
    .infra-tour-trigger-tooltip { position: absolute; right: 4.25rem; top: 50%; width: max-content; padding: .45rem .65rem; border-radius: .4rem; color: #fff; background: #1f2937; font-size: .75rem; font-weight: 600; opacity: 0; pointer-events: none; transform: translateY(-50%) translateX(.25rem); transition: opacity .18s ease, transform .18s ease; }
    .infra-tour-trigger:hover .infra-tour-trigger-tooltip, .infra-tour-trigger:focus-visible .infra-tour-trigger-tooltip { opacity: 1; transform: translateY(-50%) translateX(0); }
    @media (max-width: 640px) { .infra-tour-trigger { right: 1rem; bottom: 1rem; width: 3.25rem; height: 3.25rem; } .infra-tour-trigger-tooltip { display: none; } }
</style>

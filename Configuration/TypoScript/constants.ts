module.tx_bwdpsglist {
  view {
    # cat=module.tx_bwdpsglist/file; type=string; label=Path to template root (BE)
    templateRootPath = EXT:bw_dpsg_list/Resources/Private/Backend/Templates/
    # cat=module.tx_bwdpsglist/file; type=string; label=Path to template partials (BE)
    partialRootPath = EXT:bw_dpsg_list/Resources/Private/Backend/Partials/
    # cat=module.tx_bwdpsglist/file; type=string; label=Path to template layouts (BE)
    layoutRootPath = EXT:bw_dpsg_list/Resources/Private/Backend/Layouts/
  }
  persistence {
    # cat=module.tx_bwdpsglist//a; type=string; label=Default storage PID
    storagePid =
  }
}

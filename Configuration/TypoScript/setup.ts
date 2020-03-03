module.tx_bwdpsglist {
  view {
    templateRootPaths.0 = EXT:bw_dpsg_list/Resources/Private/Backend/Templates/
    templateRootPaths.1 = {$module.tx_bwdpsglist.view.templateRootPath}
    partialRootPaths.0 = EXT:bw_dpsg_list/Resources/Private/Backend/Partials/
    partialRootPaths.1 = {$module.tx_bwdpsglist.view.partialRootPath}
    layoutRootPaths.0 = EXT:bw_dpsg_list/Resources/Private/Backend/Layouts/
    layoutRootPaths.1 = {$module.tx_bwdpsglist.view.layoutRootPath}
  }
  persistence {
    storagePid = {$module.tx_bwdpsglist.persistence.storagePid}
  }
}

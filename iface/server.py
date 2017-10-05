from twisted.internet import reactor
from twisted.web.server import Site

from fastjsonrpc.server import JSONRPCServer

from adselect.iface import config as iface_config
from adselect.iface import utils as iface_utils
from adselect.iface import protocol as iface_proto


class AdSelectIfaceServer(JSONRPCServer):
    #campaign interface
    def jsonrpc_campaign_update(self, *campaign_data_list):
        for campaign_data in campaign_data_list:
            iface_utils.create_or_update_campaign(iface_proto.CamapaignObject(campaign_data))
        return True

    def jsonrpc_campaign_delete(self, *campaign_id_list):
        iface_utils.delete_campaign(campaign_id_list)
        return True

    #impressions interface
    def jsonrpc_impression_add(self, *impressions_data_list):
        for imobj in impressions_data_list:
            iface_utils.add_impression(iface_proto.ImpressionObject(imobj))
        return True

    #select banner interface
    def jsonrpc_banner_select(self, *impression_param_list):
        banner_requests = [iface_proto.SelectBannerRequest(impression_param)
                          for impression_param in impression_param_list]

        return [response.to_json() for response in iface_utils.select_banner(banner_requests)]


def configure_iface(port = iface_config.SERVER_PORT):
    site = Site(AdSelectIfaceServer())
    reactor.listenTCP(port, site)
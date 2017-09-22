import random, heapq, itertools

# Keep info about best paid keywords for the specific banner size
# Kesywords in the list are ordered from the best paid
# BEST_KEYWORDS:{
#   'size1':[keyword1, keyword2, ....]
#   'size2':[keyword1, keyword2, ...]
# }
BEST_KEYWORDS = {}

# KEYWORDS_BANNERS keeps sorted list of banners for given size and keyword
# KEYWORDS_BANNERS = {
#   'size1':{
#       keyword1:[(avg_pay_amount, campaignid1_bannerid1), (avg_pay_amount, campaignid2_bannerid2), ...]
#       keyword2:[(avg_pay_amount, campaignid1_bannerid1), (avg_pay_amount, campaignid2_bannerid2), ..., ...]
# },
#   'size2':{
#       keyword1:[(avg_pay_amount, campaignid1_bannerid1), (avg_pay_amount, campaignid2_bannerid2), ...]
#   }
# }
KEYWORDS_BANNERS = {}

# Keep info about banners impression payments
# KEYWORD_IMPRESSION_PAID_AMOUNT = {
#   'campaignid1_bannerid1':{
#       'keyword1':'total_payment_amount',
#       'keyword2':'total_payment_amount',
#       },
#   'campaignid2_bannerid2':{
#      'keyword1':'total_payment_amount',
#      'keyword2':'total_payment_amount',
#   }
#  }
KEYWORD_IMPRESSION_PAID_AMOUNT = {}

# Keep info about new banners to display
# NEW_BANNERS:{
#   'size1':['campaignid1_bannerid1', 'campaignid2_bannerid2'],
#   'size2':['campaignid3_bannerid3', 'campaignid1_bannerid1']
# }
NEW_BANNERS = {}

# Keep data about impressions count of banners
# BANNERS_IMPRESSIONS_COUNT = {
#   'campaignid1_bannerid1':'impression_count',
#   'campaignid2_bannerid2':'impression_count'
# }
BANNERS_IMPRESSIONS_COUNT = {}


def select_new_banners(banner_size, proposition_nb,
                       notpaid_display_cutoff=1000,
                       filtering_population_factor=4
                       ):
    """
        Return banners ids without payment statistic.
        The function doesn't allow to display banners more than notpaid_display_cutoff times without payment.
    """

    try:
        random_banners = random.sample(NEW_BANNERS.get(banner_size, []), proposition_nb*filtering_population_factor)
    except ValueError, e:
        random_banners = []

    # Filter selected banners out banners witch were displayed more times than notpaid_display_cutoff
    selected_banners = []
    for banner_id in random_banners:
        if BANNERS_IMPRESSIONS_COUNT.get(banner_id, 0) < notpaid_display_cutoff:
            selected_banners.append(banner_size)

        if len(selected_banners) > proposition_nb:
            break

    return selected_banners[:proposition_nb]


def merge(*iterables):
    """
        Sort iterables of tuples in descending mode.
    """

    h = []
    for it in map(iter, iterables):
        try:
            next = it.next
            v = next()
            h.append([(-v[0], v[1]), next])
        except StopIteration:
            pass
    heapq.heapify(h)

    while True:
        try:
            while True:
                v, next = s = h[0]
                yield -v[0], v[1]
                v = next()
                s[0] = -v[0], v[1]
                heapq._siftup(h, 0)
        except StopIteration:
            heapq.heappop(h)
        except IndexError:
            return


def select_impression_banners(banner_size,
                              impression_keywords_dict,
                              propositions_nb=100,
                              best_keywords_cutoff=100,
                              banners_per_keyword_cutoff=10,
                              mixed_new_banners_percent=5
                              ):
    """
        Select banners with appropriate size for given impression keywords.
        proposition_nb - the amount of selected banners
        best_keywords_cutoff - cutoff of the best paid keywords taking into account
        banners_per_keyword_cutoff - cutoff of the banners numbers in every seleted keywords
        mixed_new_banners_percent - approximate percentage of new banners in proposed banners list
    """

    #selected best paid impression keywords
    sbpik = set(impression_keywords_dict.keys())&set(BEST_KEYWORDS.get(banner_size, [])[:best_keywords_cutoff])

    #Select best paid banners with appropriate size

    selected_banners = []
    selected_banners_count = 0
    for avg_price, banner_id in merge(
            *[KEYWORDS_BANNERS.get(banner_size, {}).get(keyword, [])[:banners_per_keyword_cutoff] for keyword in sbpik]
    ):

        selected_banners.append(banner_id)
        selected_banners_count +=1

        if selected_banners_count >= propositions_nb:
            break

    # Add new banners without payment statistic
    new_banners_proposition_nb = int(mixed_new_banners_percent*propositions_nb/100.0)
    selected_banners += select_new_banners(banner_size, new_banners_proposition_nb)

    #Shuffle items in the list
    return random.shuffle(selected_banners)[:propositions_nb]



def update_stats(impression_keywords):
    pass


if __name__ == "__main__":
    print list(merge([(3.2, "ba"), (2, "sasd"), (1, "asdasd")],
                     [(5, "asdf"), (5, "313"), (4, "adsf")],
                     [(7, "qewr"), (3, "adsf"), (1, "ew")]))
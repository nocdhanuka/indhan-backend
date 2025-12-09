package com.dhanuka.indhan.DemoRequestEvaluated.models

data class TimeLineModel(
    var time: String? = "",
    var data: String? = "",
    var dataValue: String? = "",
    var status: Int? = 0,
    var type: String? = "",
    var comingStatus: Int? = 0,
    var selectedReceiveDate: String? = "",
    var receivedQty: String? = "",
    var approveQty: String? = ""
)
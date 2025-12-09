package com.dhanuka.indhan.institutional.model.influencers

data class InfluencersAddContactModel(
    var contact_id: Long,
    var contact_email: String,
    var contact_name: String,
    var designation: String,
    var level: String,
    var mobile_number: String,
    var wa_number: String
)
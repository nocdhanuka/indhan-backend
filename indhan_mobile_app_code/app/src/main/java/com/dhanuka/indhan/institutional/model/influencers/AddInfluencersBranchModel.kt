package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
data class AddInfluencersBranchModel(
    @PrimaryKey
    var influencerId: Long,
    var apiToken: String?,
    var office_type: Int?,
    var influencer_type: Int?,
    var parent_influencer_id: String?,
    var district: String?,
    var state: String?,
    var name: String?,
    var turn_over: String?,
    var land_area: String?,
    var influencer_category_id: String?,
    var influencer_category_type: String?,
    var state_id: String?,
    var district_id: String?,
    var address: String?,
    var latitude: Double?,
    var longitude: Double?,
    var image: String?,
    var contact_data: String?,
    var fl_ecn: String?,
    var fl_name: String?,
    var fl_type: String?,
    var hitTime: Long?,
    var status: Int?
)

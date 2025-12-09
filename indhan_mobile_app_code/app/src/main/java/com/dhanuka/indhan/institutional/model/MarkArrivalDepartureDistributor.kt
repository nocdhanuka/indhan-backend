package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class MarkArrivalDepartureDistributor {
    @PrimaryKey
    var distributor_id: Int = 0
    var arrival_comment: String? = null
    var arrival_date: String? = null
    var arrival_image: String? = null
    var arrival_lat: String? = null
    var arrival_lng: String? = null
    var departure_lat: String? = null
    var departure_lng: String? = null
    var departure_comment: String? = null
    var departure_date: String? = null
    var departure_image: String? = null
    var duration: String = "0"
    var arrival_departure_status: Int? = null
    var uploaded_server_status: Int? = null
    var server_date_time: Long? = null
}
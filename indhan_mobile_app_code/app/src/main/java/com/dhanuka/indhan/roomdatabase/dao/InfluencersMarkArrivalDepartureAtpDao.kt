package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersMarkArrivalDepartureAtpModel

@Dao
interface InfluencersMarkArrivalDepartureAtpDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersMarkArrivalDepartureAtpModel)

    @Query("SELECT * FROM InfluencersMarkArrivalDepartureAtpModel")
    fun getAllArrivalDepartureListData(): List<InfluencersMarkArrivalDepartureAtpModel?>?

    @Query("SELECT * FROM InfluencersMarkArrivalDepartureAtpModel WHERE influencer_atp_id =:id LIMIT 1")
    fun getAllArrivalDepartureById(id: Int): InfluencersMarkArrivalDepartureAtpModel?

    @Query("UPDATE InfluencersMarkArrivalDepartureAtpModel SET arrival_departure_status =:status, departure_comment =:comment, departure_date =:date, departure_image =:departureImage, departure_lat =:departureLat, departure_lng =:departureLng, uploaded_server_status =:isUploaded, server_date_time =:serverDateTime, duration =:duration WHERE influencer_atp_id =:atpId")
    fun updateDepartureData(
        status: Int,
        atpId: Int,
        comment: String,
        date: String,
        departureImage: String,
        departureLat: String,
        departureLng: String,
        isUploaded: Int,
        duration: String,
        serverDateTime: Long,
    )

    @Query("SELECT * FROM InfluencersMarkArrivalDepartureAtpModel WHERE server_date_time<=:time  LIMIT 1")
    fun getSosArrivalDepartureData(time: Long): InfluencersMarkArrivalDepartureAtpModel?

    @Query("UPDATE InfluencersMarkArrivalDepartureAtpModel SET arrival_departure_status =:status, uploaded_server_status =:isUploaded, server_date_time =:serverDateTime WHERE influencer_atp_id =:atpId")
    fun updateStatusUploadedData(
        status: Int,
        atpId: Int,
        isUploaded: Int,
        serverDateTime: Long,
    )

    @Query("DELETE FROM InfluencersMarkArrivalDepartureAtpModel WHERE influencer_atp_id =:id")
    fun deleteTableData(id: Int)

    @Query("UPDATE InfluencersMarkArrivalDepartureAtpModel SET uploaded_server_status =:status, server_date_time =:lastHitTime WHERE influencer_atp_id=:id AND ticket_number =:ticketNumber")
    fun updateArrivalDeparture(id: Int, lastHitTime: Long, status: Int, ticketNumber: String)

    @Query("SELECT * FROM InfluencersMarkArrivalDepartureAtpModel WHERE uploaded_server_status =:server_status AND server_date_time<=:currentHitTime AND arrival_departure_status =:arrival_departure_status LIMIT 1")
    fun getSingleArrivalDepartureData(
        server_status: Int,
        currentHitTime: Long,
        arrival_departure_status: Int
    ): InfluencersMarkArrivalDepartureAtpModel?

    @Query("UPDATE InfluencersMarkArrivalDepartureAtpModel SET uploaded_server_status =:status WHERE influencer_atp_id=:id")
    fun updateArrivalDepartureData(id: Int, status: Int)
}
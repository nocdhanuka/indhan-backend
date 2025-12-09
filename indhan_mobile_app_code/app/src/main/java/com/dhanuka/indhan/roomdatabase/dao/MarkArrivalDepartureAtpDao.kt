package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateMarkArrivalDepartureAtpModel

@Dao
interface MarkArrivalDepartureAtpDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: TeaEstateMarkArrivalDepartureAtpModel)

    @Query("SELECT * FROM TeaEstateMarkArrivalDepartureAtpModel")
    fun getAllArrivalDepartureListData(): List<TeaEstateMarkArrivalDepartureAtpModel?>?

    @Query("SELECT * FROM TeaEstateMarkArrivalDepartureAtpModel WHERE atp_id =:id LIMIT 1")
    fun getAllArrivalDepartureById(id: Int): TeaEstateMarkArrivalDepartureAtpModel?

    @Query("UPDATE TeaEstateMarkArrivalDepartureAtpModel SET arrival_departure_status =:status, departure_comment =:comment, departure_date =:date, departure_image =:departureImage, departure_lat =:departureLat, departure_lng =:departureLng, uploaded_server_status =:isUploaded, server_date_time =:serverDateTime, duration =:duration WHERE atp_id =:atpId")
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

    @Query("SELECT * FROM TeaEstateMarkArrivalDepartureAtpModel WHERE server_date_time<=:time  LIMIT 1")
    fun getSosArrivalDepartureData(time: Long): TeaEstateMarkArrivalDepartureAtpModel?

    @Query("UPDATE TeaEstateMarkArrivalDepartureAtpModel SET arrival_departure_status =:status, uploaded_server_status =:isUploaded, server_date_time =:serverDateTime WHERE atp_id =:atpId")
    fun updateStatusUploadedData(
        status: Int,
        atpId: Int,
        isUploaded: Int,
        serverDateTime: Long,
    )

    @Query("DELETE FROM TeaEstateMarkArrivalDepartureAtpModel WHERE atp_id =:id")
    fun deleteTableData(id: Int)

    @Query("UPDATE TeaEstateMarkArrivalDepartureAtpModel SET uploaded_server_status =:status, server_date_time =:lastHitTime WHERE atp_id=:id AND ticket_number =:ticketNumber")
    fun updateArrivalDeparture(id: Int, lastHitTime: Long, status: Int, ticketNumber: String)

    @Query("SELECT * FROM TeaEstateMarkArrivalDepartureAtpModel WHERE uploaded_server_status =:server_status AND server_date_time<=:currentHitTime AND arrival_departure_status =:arrival_departure_status LIMIT 1")
    fun getSingleArrivalDepartureData(
        server_status: Int,
        currentHitTime: Long,
        arrival_departure_status: Int
    ): TeaEstateMarkArrivalDepartureAtpModel?

    @Query("UPDATE TeaEstateMarkArrivalDepartureAtpModel SET uploaded_server_status =:status WHERE atp_id=:id")
    fun updateArrivalDepartureData(id: Int, status: Int)
}
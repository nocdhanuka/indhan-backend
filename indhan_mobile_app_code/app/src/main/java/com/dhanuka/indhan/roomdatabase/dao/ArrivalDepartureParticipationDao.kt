package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline

@Dao
interface ArrivalDepartureParticipationDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ArrivalDepartureModelOffline)

    @Query("SELECT * FROM ArrivalDepartureModelOffline WHERE isUploaded =:status AND type =:type")
    fun getAllArrivalDepartureData(
        status: Int,
        type: Int,
    ): List<ArrivalDepartureModelOffline>?

    @Query("SELECT * FROM ArrivalDepartureModelOffline")
    fun getAllArrivalDepartureData(): List<ArrivalDepartureModelOffline>?

    @Query("SELECT * FROM ArrivalDepartureModelOffline WHERE isUploaded =:status LIMIT 1")
    fun getSingleArrivalDepartureData(status: Int): ArrivalDepartureModelOffline?

    @Query("SELECT * FROM ArrivalDepartureModelOffline WHERE isUploaded =:status AND lastHitTime<=:time AND type =:type LIMIT 1")
    fun getSingleArrivalDepartureData(
        status: Int,
        time: Long,
        type: Int
    ): ArrivalDepartureModelOffline?

    @Query("SELECT * FROM ArrivalDepartureModelOffline WHERE lastHitTime<=:time  LIMIT 1")
    fun getSosArrivalDepartureData(time: Long): ArrivalDepartureModelOffline?

    @Query("SELECT * FROM ArrivalDepartureModelOffline WHERE ticket_no=:ticket_no AND ticket_id =:ticket_id LIMIT 1")
    fun getArrivalDepartureData(
        ticket_id: String,
        ticket_no: String?
    ): ArrivalDepartureModelOffline?

    @Query("UPDATE ArrivalDepartureModelOffline SET isUploaded =:status WHERE id=:id")
    fun updateArrivalDepartureData(id: Int, status: Int)

    @Query("UPDATE ArrivalDepartureModelOffline SET isUploaded =:status ,lastHitTime =:lastHitTime WHERE id=:id AND ticket_no =:ticketNumber")
    fun updateArrivalDeparture(id: Int, lastHitTime: Long, status: Int, ticketNumber: String)

    @Query("UPDATE ArrivalDepartureModelOffline SET type =:type ,departure_time =:departure_time ,departure_latitude =:departure_lat ,departure_longitude =:departure_lng ,departure_imageURL =:departure_img ,duration =:duration ,departure_comment =:departure_comment ,lastHitTime =:hitTime WHERE id =:id")
    fun updateArrivalDepartureData(
        id: Int,
        type: Int,
        departure_time: String,
        departure_comment: String,
        departure_lat: String,
        departure_lng: String,
        departure_img: String,
        duration: String,
        hitTime: Long
    )

    @Query("DELETE FROM ArrivalDepartureModelOffline WHERE id =:id")
    fun deleteArrivalDepartureData(id: Int)
}
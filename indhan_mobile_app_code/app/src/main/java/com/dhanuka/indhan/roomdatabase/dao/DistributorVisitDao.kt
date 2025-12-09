package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.DistributorVisit

@Dao
interface DistributorVisitDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: DistributorVisit)

    @Query("SELECT * FROM DistributorVisit")
    fun getAllArrivalDepartureListData(): List<DistributorVisit?>?

    @Query("SELECT * FROM DistributorVisit WHERE distributor_advisor_id =:id")
    fun getAllArrivalDepartureById(id: Int): List<DistributorVisit?>?
}
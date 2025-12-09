package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestMaterialModel

@Dao
interface RequestMaterialModelDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: RequestMaterialModel)

    @Query("SELECT * FROM RequestMaterialModel")
    fun getAllMaterialRequestData(): List<RequestMaterialModel?>?

    @Query("SELECT * FROM RequestMaterialModel WHERE timeStamp<=:time  LIMIT 1")
    fun getSosMaterialRequestData(time: Long): RequestMaterialModel?

    @Query("SELECT * FROM RequestMaterialModel WHERE isUploaded =:isUploaded AND timeStamp<=:currentHitTime LIMIT 1")
    fun getSingleRequestMaterial(isUploaded: Boolean, currentHitTime: Long): RequestMaterialModel?

    @Query("SELECT * FROM RequestMaterialModel WHERE isUploaded =:isUploaded LIMIT 1")
    fun getSingleRequestMaterial(isUploaded: Boolean): RequestMaterialModel?

    @Query("DELETE FROM RequestMaterialModel WHERE id =:id")
    fun deleteTableData(id: Int)

    @Query("UPDATE RequestMaterialModel SET isUploaded =:isUpload ,timeStamp =:currentHitTimeTwoHour WHERE id=:id")
    fun updateRequestMaterialData(id: Int, currentHitTimeTwoHour: Long, isUpload: Boolean)
}
package com.dhanuka.indhan.ACPVisitHistory

import android.app.Dialog
import android.content.Context
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.Window
import android.widget.ImageView
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemAcpVisitHistoryBinding
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean


class ACPVisitHistoryListAdapter(
    var context: Context,
    var list: MutableList<VisitHistoryFarmerBean.Data>,
    var listenenr: ACPListener
) : RecyclerView.Adapter<ACPVisitHistoryListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemAcpVisitHistoryBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_acp_visit_history, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemAcpVisitHistoryBinding) :
        RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {

            view.txtVillageName.text = list[position].village ?: ""
            view.txtFarmersName.text = list[position].farmerName
            view.txtDateValue.text = list[position].createdAt
            view.txtDate.text = list[position].crop

            if (list[position].callStatus == "0") {
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_ylw))


                view.imgCall.setOnClickListener {
                    listenenr.onCall(position)
                }

            } else {

                view.imgCall.setOnClickListener {
                    listenenr.onCall(position)
                }
                view.imgCall.setImageDrawable(context.resources.getDrawable(R.drawable.call_green))
            }

//
//            view.imgCall.setOnClickListener {
//                listenenr.onCall(position)
//            }
//
//            view.imgSetting.setOnClickListener {
//                listenenr.onChanageStatus(position)
//            }


            if (list[position].leadStatus == "0") {
                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_grey))

                view.imgSetting.setOnClickListener {
                    listenenr.onChanageStatus(position)
                }
            } else {
                view.imgSetting.setOnClickListener {
                    listenenr.onChanageStatus(position)
                }
                view.imgSetting.setImageDrawable(context.resources.getDrawable(R.drawable.leads_green))

            }

            Glide.with(context)
                .asBitmap()
                .load(list[position].visitImage)
                .into(view.imgView)
            view.imgView.setOnClickListener {
                if (!TextUtils.isEmpty(list[position].visitImage)) {
                    showDialogImage(context, list[position].visitImage)
                }
            }
        }
    }

    private fun showDialogImage(context: Context, file: String) {
        val dialog = Dialog(context)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        val requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE) // because file name is always same
            .skipMemoryCache(true)

        Glide.with(context)
            .load(file)
            .apply(requestOptions)
            .into(msg)
        dialog.show()
    }
}
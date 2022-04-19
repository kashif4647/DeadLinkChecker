<?php
function createTemplate($results){
    ?>
    <table>
        <tr>
            <th>Post ID</th>
            <th>Dead Link</th>
        </tr>
        <?php
        foreach ($results as $key => $value) { ?>
            <tr>
                <td>
                    <?= $value->post_id ?>
                </td>
                <td>
                    <?= $value->dead_link ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>

    <?php
}